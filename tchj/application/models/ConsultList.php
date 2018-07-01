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
            return $this->db->count_all('consultlist');
        }else{
            return $this->db->where($where)->count_all_results('consultlist');
        }
    }


    # 请示相关统计  给首页用
    # return
    /*
     Array
(
    [total] => 8        # 总的请示数
    [department_data] => Array
        (
            [1] => Array
                (
                    [pid] => 1      # 部门
                    [pname] => 太仓海警支队   # 部门名称
                    [ongoing_cnt] => 3      # 正在进行的请示数
                    [revoke_cnt] => 1       # 撤销的请示数
                    [complete_cnt] => 1     # 完成的请示数
                    [total] => 4            # 总的请示数(不含撤销的)
                )
        )

    )
    */
    public function consult_statistics()
    {
        # 总数
        $total = $this->query_total('status != 3');

        # 分部门来查询进行中的(status = 1 or status = 0)
        $ongoing_data = $this->query_department_count('c.status IN (0, 1)');

        # 撤销的数量
        $revoke_data = $this->query_department_count('c.status = 3');

        # 完成的数量
        $complete_data = $this->query_department_count('c.status = 2');

        $department_data = array();
        foreach ($ongoing_data as $val) {
            $department_data[$val['pid']] = array(
                'pid' => $val['pid'],
                'pname' => $val['pname'],
                'ongoing_cnt' => $val['cnt'],
                'revoke_cnt' => 0,
                'complete_cnt' => 0,
                'total' => 0
            );
            foreach ($revoke_data as $rv) {
                $department_data[$rv['pid']]['revoke_cnt'] = $rv['cnt'];
            }
            foreach ($complete_data as $cv) {
                $department_data[$cv['pid']]['complete_cnt'] = $cv['cnt'];
            }
            $department_data[$val['pid']]['total'] = $department_data[$val['pid']]['ongoing_cnt'] + $department_data[$val['pid']]['complete_cnt'];
        }

        $result = array(
            'total' => $total,
            'department_data' => $department_data
        );

        return $result;
    }

    public function query_department_count($where='')
    {
        $field = 'c.pid, count(c.id) cnt, d.pname';
        $this->db->from('consultlist c')->join('department d', 'c.pid = d.pid')->select($field)->where($where);

        $result = $this->db->group_by('c.pid')->get()->result_array();

        return $result;
    }
}

?>