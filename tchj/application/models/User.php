<?php
//用户操作类
class User extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}
	//$this->db->last_query();  用于展示最近执行的SQL语句
	
	//用户登录验证 成功的话返回相关信息 用于生成SESSION
	public function userlogin($username,$password){
		$return=rexGetMReturn();
		$this->db->select('uid,username,user.tid,tname,pid,jid');
		$this->db->from('user');
		$this->db->join('usertype','user.tid=usertype.tid','inner');
		$this->db->where('username',$username);
		$this->db->where('userpwd',$password);
		$result=$this->db->get();
		if (!$result){
			$return['code']=500;
			$return['message']='登录查询模块出错，请管理员核查';
			return $return;
		}
		if ($result->num_rows()!=1){
			$return['code']=450;
			$return['message']='用户名或者密码错误';
		}else{
			$return['data']=$result->row_array();
		}
		return $return;
	}
	
	//获得用户列表信息
	public function user($arr=''){
		$return=rexGetMReturn();
		$this->db->select('uid,username,user.tid,department.pid,job.jid,realname,telnumber,usertype.tname,department.pname,job.jname');
		$this->db->from('user');
		$this->db->join('usertype','user.tid=usertype.tid','inner');
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
				//$return['message']=$this->db->last_query();
				$return['message']='用户列表信息部件出错，请工程师核查';
				return $return;
			}
			$return['data']=$query->result_array();
		}else{
			$this->db->order_by('uid','ASC');
			$query=$this->db->get();
			//die($this->db->last_query());
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
			$return['code']=401;
			$return['message']='该用户名的账户已经存在，无法重复添加';
			return $return;
		}
		if ($arr['pid']!=0){
			//检查部门编号是否存在
			$this->db->select('pid');
			$this->db->where('pid',$arr['pid']);
			$this->db->from('department');
			if ($this->db->count_all_results()<=0){
				$return['code']=401;
				$return['message']='部门编号错误';
				return $return;
			}
		}
		if ($arr['jid']!=0){
			//检查职务编号是否存在
			$this->db->select('jid');
			$this->db->where('jid',$arr['jid']);
			$this->db->from('job');
			if ($this->db->count_all_results()<=0){
				$return['code']=500;
				$return['message']='职务编号错误';
				return $return;
			}
		}
		//NOTICE  tid是否规范交给controller层判定 tid=USERM tid=USERL.....
		//插入数据
		$row=array(
			'username'=>$arr['username'],
			'userpwd'=>$arr['password'],
			'tid'=>$arr['tid'],
			'pid'=>$arr['pid'],
			'jid'=>$arr['jid'],
			'realname'=>$arr['realname'],
			'telnumber'=>$arr['telnumber']
		);
		$query=$this->db->insert('user',$row);
		if (!$query){
			$return['code']=550;
			//$return['message']=$this->db->last_query();
			$return['message']='新增用户功能部件出现问题，请联系管理员核查';
		}else{
			$return['data']=$this->db->insert_id();
		}
		return $return;
	}
	
	//修改用户  //目前只能修改密码 名字和电话号码，部门职务类型可能涉及复杂逻辑判定，暂不支持修改
	public function useredit($arr=''){
		$return=rexGetMReturn();
		$resetPwd=false;
		if ($arr['manager']){
			if ($arr['newpassword']) $resetPwd=true;
		}else{
			if ($arr['newpassword']!=''){
				$this->db->select('uid');
				$this->db->where('uid',$arr['uid']);
				$this->db->where('userpwd',$arr['oldpassword']);
				$this->db->from('user');
				if ($this->db->count_all_results()<=0){
					$return['code']=401;
					$return['message']='用户的原始密码不正确，无法修改密码';
					return $return;
				}
				$resetPwd=true;
			}
		}
		//插入数据
		if ($resetPwd==true){
			$row=array(
				'userpwd'=>$arr['newpassword'],
				'realname'=>$arr['realname'],
				'telnumber'=>$arr['telnumber']
			);
		}else{
			$row=array(
				'realname'=>$arr['realname'],
				'telnumber'=>$arr['telnumber']
			);
		}
		$this->db->where('uid',$arr['uid']);
		$query=$this->db->update('user',$row);
		if (!$query){
			$return['code']=550;
			//$return['message']=$this->db->last_query();
			$return['message']='用户修改功能出现问题，请工程师核查';
		}
		return $return;
	}
	
	//删除用户 管理员无法删除 有业务数据的用户无法删除，只做禁用
	public function userdrop($arr=''){
		$return=rexGetMReturn();
		$this->db->select('tid');
		$this->db->where('uid',$arr['uid']);
		$query=$this->db->get('user');
		if ($query->num_rows()!=1){
			$return['code']=401;
			$return['message']='没有找到需要删除的用户信息';
			return $return;
		}
		$user=$query->row_array();
		if ($user['tid']==USERM){
			$return['code']=403;
			$return['message']='管理员无法删除';
			return $return;
		}
		//是否有业务数据的判定
		
		$this->db->delete('user', array('uid'=>$arr['uid']));
		return $return;
	}
}