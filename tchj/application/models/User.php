<?php
class User extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	//用户登录验证
	public function userlogin($username, $password){
		$where = array(
			'username' => $username,
			'userpwd' => md5($password)
		);
		$this->db->select('*')->from('user')->where($where);
		$query = $this->db->get();
		$result = $query->result_array();

		return $result;
	}
	
	//获得用户列表信息
	public function user($arr=''){
		$return=rexGetMReturn();
		$this->db->select('uid,username,tid,department.pid,job.jid,realname,telnumber,department.pname,job.jname');
		$this->db->from('user');
		$this->db->join('department','user.pid=department.pid','left');
		$this->db->join('job','user.jid=job.jid','left');
		if (is_array($arr) && count($arr)>0){
			$check=array('uid','username','pid','tid','jid');
			for ($i=0; $i<count($check); $i++){
				if (!isset($arr[$check[$i]])) continue;
				if (is_array($arr[$check[$i]]) && count($arr[$check[$i]])>0){
					$this->db->where_in($check[$i],$arr[$check[$i]]);
				}else{
					$this->db->where($check[$i],$arr[$check[$i]]);
				}
			}
			if (isset($arr['realname']) && $arr['realname']){
				$this->db->like('realname',$arr['realname']);
			}
			$query=$this->db->get();
			if (!$query){
				$return['code']=550;
				$return['message']=$this->db->last_query();
				return $return;
			}
			$return['data']=$query->result_array();
		}else{
			$this->db->order_by('uid','ASC');
			$query=$this->db->get();
			$return['data']=$query->result_array();
		}
		return $return;
	}
	
	//添加用户
	public function useradd($arr=''){
		$return=rexGetMReturn();
		//检查用户名是否重复
		$this->db->select('uid');
		$this->db->where('username',$arr['username']);
		$this->db->from('user');
		if ($this->db->count_all_results()>0){
			$return['code']=500;
			$return['message']='该用户名的账户已经存在，无法重复添加';
			return $return;
		}
		//检查部门编号是否存在
		$this->db->select('pid');
		$this->db->where('pid',$arr['pid']);
		$this->db->from('department');
		if ($this->db->count_all_results()<=0){
			$return['code']=500;
			$return['message']='部门编号错误';
			return $return;
		}
		//检查职务编号是否存在
		$this->db->select('jid');
		$this->db->where('jid',$arr['jid']);
		$this->db->from('job');
		if ($this->db->count_all_results()<=0){
			$return['code']=500;
			$return['message']='职务编号错误';
			return $return;
		}
		//插入数据
		$row=array(
			'username'=>$arr['username'],
			'userpwd'=>$arr['userpwd'],
			'tid'=>$arr['tid'],
			'pid'=>$arr['pid'],
			'jid'=>$arr['jid'],
			'realname'=>$arr['realname'],
			'telnumber'=>$arr['telnumber']
		);
		$query=$this->db->insert('user',$row);
		if (!$query){
			$return['code']=550;
			$return['message']=$this->db->last_query();
		}else{
			$return['data']=$this->db->insert_id();
		}
		return $return;
	}
	
	//修改用户  //目前只能修改密码 名字和电话号码，部门职务类型可能涉及复杂逻辑判定，暂不支持修改
	public function useredit($arr=''){
		
	}
	
	//删除用户
	public function userdel($uid=''){
	}
}