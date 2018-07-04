<?php
class ConsultList extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    # 因为consultlist 和consultreturn都需要和user关联  这里就偷懒了  该方法复用性很低,偷懒而已
    public function query($where=array(), $field="*", $limit=10, $offset=0, $table='consultlist c')
    {
        $this->db->from($table)->join('user u', 'c.uid = u.uid')->select($field)->where($where);
        $query = $this->db->limit($limit, $offset)->get();
        $result = $query->result_array();

        return $result;
    }

    public function query_total($where=array())
    {
        if (empty($where)) {
            return $this->db->count_all('consultlist c');
        }else{
            return $this->db->where($where)->count_all_results('consultlist c');
        }
    }

    public function consult_statistics($pid)
    {
        # 先看一下当前用户所在部门级别
        $user_department = $this->db->where('pid', $pid)->get('department')->result_array();
        # $plevel 表示部门级别  0可以看到所有部门的  1只能看到本部门收到的请示数  2只能看到本部门发送的请示数
        $plevel = $user_department[0]['plevel'];

        $return = array();
        switch ($plevel) {
            case 0:
                $where = 'plevel = 1';
                $return = $this->boss_statistics($where);
                break;

            case 1:
                $where = "pid = {$user_department[0]['pid']}";
                $return = $this->boss_statistics($where);
                break;

            default:
                $where = "pid = {$user_department[0]['pid']}";
                $return = $this->boss_statistics($where, 1);
        }
        $return['pid'] = $user_department[0]['pid'];
        $return['pname'] = $user_department[0]['pname'];
        return $return;
    }

    public function boss_statistics ($where, $user_type=0)
    {
        # 查询出plevel = 1的部门
        $departments = $this->db->where($where)->get('department')->result_array();

        $return = array(
            'total' => 0,
            'total_ongoing' => 0,
            'total_complete' => 0,
            'total_revoke' => 0,
            'departments' => array()
        );
        foreach ($departments as $department) {
            $data = $this->_consult_statistics($department['pid'], $user_type);
            $data['pname'] = $department['pname'];
            $data['pid'] = $department['pid'];
            $return['total'] += $data['total_cnt'];
            $return['total_ongoing'] += $data['ongoing_cnt'];
            $return['total_complete'] += $data['complete_cnt'];
            $return['total_revoke'] += $data['revoke_cnt'];
            array_push($return['departments'], $data);
        }

        return $return;
    }

    # 各部门的统计
    public function _consult_statistics($pid, $user_type=0)
    {
        # user_type 代表是不是上级 0上级 1下级
        if ($user_type == 0) {
            $where = " pid = {$pid}";
        }else{
            $where = " created_pid = {$pid}";
        }
        $field = 'pid, status';
        $query = $this->db->from('consultlist')->select($field)->where($where);
        $rows = $query->get()->result_array();
        $ongoing_cnt = 0;
        $complete_cnt = 0;
        $revoke_cnt = 0;

        foreach ($rows as $row) {
            switch($row['status']){
                case 0:
                case 1:
                    $ongoing_cnt += 1;
                    break;
                case 2:
                    $complete_cnt += 1;
                    break;
                case 3:
                    $revoke_cnt += 1;
                    break;
            }
        }
        $total_cnt = $ongoing_cnt + $complete_cnt;

        $return = array(
            'total_cnt' => $total_cnt,
            'ongoing_cnt' => $ongoing_cnt,
            'complete_cnt' => $complete_cnt,
            'revoke_cnt' => $revoke_cnt
        );

        return $return;
    }
}

?>