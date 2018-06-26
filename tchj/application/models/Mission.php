<?php
class Mission extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//获得任务列表
	public function mission($arr = '')
	{
		$pagenum = 20;  //每页显示数量
		$user = rexGetSession('user');
		$return = rexGetMReturn();

		//获得权限下的任务列表
		$sql = 'SELECT * FROM missionlist WHERE ';
		$sql2 = 'SELECT COUNT(mid) AS a FROM missionlist WHERE ';
		$real = '';
		switch ($user['tid']) {
			case 1: //管理员
				$real = '1=1';
				break;
			case 2: //支队领导
				$real = 'status>0 OR author=' . $user['uid'];
				break;
			case 3: //上级用户
				$real = '(author=' . $user['uid'] . ' AND status<>-1 OR (status>0 AND pass=\'0\'))';
				break;
			case 4: //下级用户
				$sql = 'SELECT A.* FROM missionlist AS A INNER JOIN missiondepartment AS B ON A.mid=B.mid WHERE A.status>0 AND (B.pid=' . $_SESSION['user']['pid'] . ' OR B.pid=0)';
				$sql2 = 'SELECT COUNT(a.mid) AS a FROM missionlist AS A INNER JOIN missiondepartment AS B ON A.mid=B.mid WHERE A.status>0 AND (B.pid=' . $_SESSION['user']['pid'] . ' OR B.pid=0)';
				break;
		}
		if (isset($arr['status'])) {
			$real .= ' AND status=' . $arr['status'];
		}
		if (isset($arr['timeout'])) {
			$real .= ' AND timeout=' . $arr['timeout'];
		}
		if (isset($arr['datestart'])) {
			$real .= ' AND datestart>=\'' . $arr['datestart'] . '\'';
		}
		if (isset($arr['dateend'])) {
			$real .= ' AND dateend<>\'0000-00-00\' AND dateend<=\'' . $arr['dateend'] . '\'';
		}
		if (isset($arr['key'])) {
			$real .= ' AND mtitle like \'%' . $arr['key'] . '%\'';
		}
		$sql = $sql . $real . ' ORDER BY mid DESC';
		$sql2 = $sql2 . $real;
		if (isset($arr['nowpage'])) {
			$num1 = $pagenum * ($arr['nowpage'] - 1);
		} else {
			$num1 = 0;
		}
		$sql .= ' LIMIT ' . $num1 . ',' . $pagenum;
		$query = $this->db->query($sql);
		$query2 = $this->db->query($sql2);
		$temp = $query2->row_array();
		$return['data'] = $query->result_array();
		$return['nowpage'] = $arr['nowpage'];
		$return['allcount'] = $temp['a'];
		return $return;
	}

	//任务信息操作
	public function missionOP($op, $arr)
	{
		$return = rexGetMReturn();
		switch ($op) {
			case 'add':
				$row = array(
					'mtitle' => $arr['title'],
					'datemake' => date("Y-m-d"),
					'datestart' => $arr['dateSta'],
					'dateend' => $arr['dateEnd'],
					'mcontent' => $arr['intro'],
					'annex' => json_encode($arr['annex']),
					'author' => $_SESSION['user']['uid'],
					'tips' => $arr['tip'],
					'status' => 0,
					'timeout' => 0,
					'rcount' => 0,
					'pass' => ''
				);
				if ($row['pass'] != '0') {
					//检测当前的发布部门是否存在
					$this->db->select('pid');
					$this->db->where_in('pid', $arr['power']);
					$this->db->from('department');
					if ($this->db->count_all_results() <= 0) {
						$return['code'] = 500;
						$return['message'] = '任务指派部门参数有误 ';
						return $return;
					};
					$row['pass'] = implode(',', $arr['power']);
				}
				//结束时间为空，则不做设置，使用数据库定义的默认值
				if ($row['dateend'] == '') unset($row['dateend']);
				$result = $this->db->insert('missionlist', $row);
				if (!$result) {
					$return['code'] = 550;
					$return['message'] = '插入任务数据出现问题，请工程师核查 ';
					return $return;
				}
				//获得新插入的任务id编号
				$nowid = $this->db->insert_id();
				if ($row['pass'] == '0') {
					$result = $this->db->insert('missiondepartment', array('mid' => $nowid, 'pid' => 0));
				} else {
					$row = array();
					for ($i = 0; $i < count($arr['power']); $i++) {
						array_push($row, array('mid' => $nowid, 'pid' => $arr['power'][$i]));
					}
					$result = $this->db->insert_batch('missiondepartment', $row);
				}
				if (!$result) {
					$return['code'] = 550;
					$return['message'] = '插入任务数据出现问题，请工程师核查 ';
				} else {
					$return['data'] = $nowid;
				}
				return $return;
				break;
			case 'edit':    //修改任务信息
				break;
			case 'del':     //删除任务信息
				break;
		}
	}
}