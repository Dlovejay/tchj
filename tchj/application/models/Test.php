<?php
//部门信息，职务信息，用户类别的操作
class Test extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	private function rexGetMReturn(){
		return array('code'=>0,'message'=>'','data'=>'');
	}
	
	//查询部门信息
	public function department($arr=''){
		$return=$this->rexGetMReturn();
		if (is_array($arr) && count($arr)>0){
			$this->db->select('*');
			$check=array('pid','plevel');
			for ($i=0; $i<count($check); $i++){
				if (!isset($arr[$check[$i]])) continue;
				if (is_array($arr[$check[$i]]) && count($arr[$check[$i]])>0){
					$this->db->where_in($check[$i],$arr[$check[$i]]);
				}else{
					$this->db->where($check[$i],$arr[$check[$i]]);
				}
			}
			if (isset($arr['pname']) && $arr['pname']){
				$this->db->like('pname',$arr['pname']);
			}
			$query=$this->db->get('department');
			if (!$query){
				$return['code']=550;
				$return['message']='部门查询部件错误，请工程师核查';
				return $return;
			}
			$return['data']=$query->result_array();
		}else{
			$this->db->select('*');
			$this->db->order_by('pid','ASC');
			$query=$this->db->get('department');
			$return['data']=$query->result_array();
		}
		return $return;
	}
	//编辑部门信息
	public function departmentOP($op,$arr){
		$return=$this->rexGetMReturn();
		switch($op){
			case 'add':   //增加部门信息
				//检查名称是否重复
				$this->db->select('pid');
				$this->db->where('pname',$arr['text']);
				$this->db->from('department');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='已经存在该名称的部门';
					return $return;
				}
				//插入数据
				$row=array(
					'pname'=>$arr['text'],
					'plevel'=>$arr['level'],
				);
				$query=$this->db->insert('department',$row);
				if (!$query){
					$return['code']=550;
					$return['message']='插入部门数据出现问题，请工程师核查';
				}else{
					$return['data']=$this->db->insert_id();
				}
				return $return;
			case 'edit':   //修改部门信息 目前只允许修改部门名称
				//检查名称是否重复
				$this->db->select('pid');
				$this->db->where('pname',$arr['text']);
				$this->db->from('department');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='已经存在该名称的部门';
					return $return;
				};
				$row=array(
					'pname'=>$arr['text']
				);
				$this->db->where('pid',$arr['id']);
				$query=$this->db->update('department',$row);
				if (!$query){
					$return['code']=550;
					$return['message']='部门信息修改失败，请工程师核查';
				}else if($this->db->affected_rows()==0){
					$return['code']=500;
					$return['message']='部门信息没有被修改';
				}
				return $return;
			case 'del':  //删除部门信息
				//检查当前部门是否已经有用户存在
				$this->db->select('uid');
				$this->db->where('pid',$arr['id']);
				$this->db->from('user');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='该部门下已经存在用户信息，请确保要操作的部门无对应用户信息后再删除';
					return $return;
				};
				//检查当前任务权限已经含有当前部门，有则目前也不能删除
				$this->db->select('mid');
				$this->db->where('pid',$arr['id']);
				$this->db->from('missiondepartment');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='该部门下已经存在可查看的任务信息，暂时无法删除';
					return $return;
				};
				$this->db->where('pid',$arr['id']);
				$query=$this->db->delete('department');
				if (!$query){
					$return['code']=550;
					$return['message']='删除部门数据出现问题，请工程师核查';
				}else if($this->db->affected_rows()==0){
					$return['code']=500;
					$return['message']='没有任何部门信息被删除';
				}
				return $return;
		}
	}
	
	//查询职务信息
	public function job($arr=''){
		$return=$this->rexGetMReturn();
		$this->db->select('job.*,pname');
		$this->db->from('job');
		$this->db->join('department','job.pid=department.pid','inner');
		if (is_array($arr) && count($arr)>0){
			$check=array('jid','pid');
			for ($i=0; $i<count($check); $i++){
				if (!isset($arr[$check[$i]])) continue;
				if (is_array($arr[$check[$i]]) && count($arr[$check[$i]])>0){
					$this->db->where_in($check[$i],$arr[$check[$i]]);
				}else{
					$this->db->where($check[$i],$arr[$check[$i]]);
				}
			}
			if (isset($arr['jname']) && $arr['jname']){
				$this->db->like('jname',$arr['jname']);
			}
			$query=$this->db->get();
			if (!$query){
				$return['code']=550;
				$return['message']='职务查询部件错误，请工程师核查';
				return $return;
			}
			$return['data']=$query->result_array();
		}else{
			$this->db->order_by('jid','ASC');
			$query=$this->db->get();
			$return['data']=$query->result_array();
		}
		return $return;
	}
	//编辑职务信息
	public function jobOP($op,$arr){
		$return=$this->rexGetMReturn();
		switch($op){
			case 'add':   //增加职务信息
				//检查对应的部门信息是否存在
				$this->db->select('pid');
				$this->db->where('pid',$arr['level']);
				$this->db->from('department');
				if ($this->db->count_all_results()<=0){
					$return['code']=500;
					$return['message']='职务对应的部门参数错误';
					return $return;
				};
				//检查名称是否重复
				$this->db->select('jid');
				$this->db->where('jname',$arr['text']);
				$this->db->where('pid',$arr['level']);
				$this->db->from('job');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='在该部门无法添加同样名称的职务';
					return $return;
				};
				//插入数据
				$row=array(
					'jname'=>$arr['text'],
					'pid'=>$arr['level'],
				);
				$query=$this->db->insert('job',$row);
				if (!$query){
					$return['code']=550;
					$return['message']='插入职务数据出现问题，请工程师核查';
				}else{
					$return['data']=$this->db->insert_id();
				}
				return $return;
			case 'edit':   //修改职务信息
				//检查对应的部门信息是否存在
				$this->db->select('pid');
				$this->db->where('pid',$arr['level']);
				$this->db->from('department');
				if ($this->db->count_all_results()<=0){
					$return['code']=500;
					$return['message']='职务对应的部门参数错误';
					return $return;
				}
				//检查名称是否重复
				$this->db->select('jid');
				$this->db->where('jname',$arr['text']);
				$this->db->where('pid',$arr['level']);
				$this->db->from('job');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='在该部门无法添加同样名称的职务';
					return $return;
				};
				$row=array(
					'jname'=>$arr['text'],
					'pid'=>$arr['level'],
				);
				$this->db->where('jid',$arr['id']);
				$query=$this->db->update('job',$row);
				if (!$query){
					$return['code']=550;
					$return['message']='职务信息修改失败，请工程师核查';
				}else if($this->db->affected_rows()==0){
					$return['code']=500;
					$return['message']='职务信息没有被修改';
				}
				return $return;
			case 'del':  //删除职务信息
				//检查当前部门是否已经有用户存在
				$this->db->select('uid');
				$this->db->where('jid',$arr['id']);
				$this->db->from('user');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='该职务下已经存在用户信息，请确保要操作的职务无对应用户信息后再删除';
					return $return;
				};
				$this->db->where('jid',$arr['id']);
				$query=$this->db->delete('job');
				if (!$query){
					$return['code']=550;
					$return['message']='删除职务数据出现问题，请工程师核查';
				}else if($this->db->affected_rows()==0){
					$return['code']=500;
					$return['message']='没有任何职务信息被删除';
				}
				return $return;
		}
	}
	
	//查询账户类型信息
	public function usertypeInfor($arr=''){
		$return=$this->rexGetMReturn();
		if (is_array($arr) && count($arr)>0){
			$this->db->select('*');
			$check=array('tid','tname');
			for ($i=0; $i<count($check); $i++){
				if (!isset($arr[$check[$i]])) continue;
				if (is_array($arr[$check[$i]]) && count($arr[$check[$i]])>0){
					$this->db->where_in($check[$i],$arr[$check[$i]]);
				}else{
					$this->db->where($check[$i],$arr[$check[$i]]);
				}
			}
			$query=$this->db->get('usertype');
			if (!$query){
				$return['code']=550;
				$return['message']='用户类型查询部件错误，请工程师核查';
				return $return;
			}
			$return['data']=$query->result_array();
		}else{
			$this->db->select('*');
			$this->db->order_by('tid','ASC');
			$query=$this->db->get('usertype');
			$return['data']=$query->result_array();
		}
		return $return;
	}
	
	//获得用户列表信息
	public function user($arr=''){
		$return=$this->rexGetMReturn();
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
	
	//获得任务列表
	public function mission($arr=''){
		$pagenum=20;  //每页显示数量
		$user=$_SESSION['user'];
		$return=$this->rexGetMReturn();
		
		//获得权限下的任务列表
		$sql='SELECT * FROM missionlist WHERE ';
		$sql2='SELECT COUNT(mid) AS a FROM missionlist WHERE ';
		$real='';
		switch($user['tid']){
			case 1: //管理员
				$real='1=1';
				break;
			case 2: //支队领导
				$real='status>0 OR author='.$user['uid'];
				break;
			case 3: //上级用户
				$real='(author='.$user['uid'].' AND status<>-1 OR (status>0 AND pass=\'0\'))';
				break;
			case 4: //下级用户
				$sql='SELECT A.* FROM missionlist AS A INNER JOIN missiondepartment AS B ON A.mid=B.mid WHERE A.status>0 AND (B.pid='.$_SESSION['user']['pid'].' OR B.pid=0)';
				$sql2='SELECT COUNT(a.mid) AS a FROM missionlist AS A INNER JOIN missiondepartment AS B ON A.mid=B.mid WHERE A.status>0 AND (B.pid='.$_SESSION['user']['pid'].' OR B.pid=0)';
				break;
		}
		if (isset($arr['status'])){
			$real.=' AND status='. $arr['status'];
		}
		if (isset($arr['timeout'])){
			$real.=' AND timeout='. $arr['timeout'];
		}
		if (isset($arr['datestart'])){
			$real.=' AND datestart>=\''. $arr['datestart'] . '\'';
		}
		if (isset($arr['dateend'])){
			$real.=' AND dateend<>\'0000-00-00\' AND dateend<=\''. $arr['dateend'] . '\'';
		}
		if (isset($arr['key'])){
			$real.=' AND mtitle like \'%'. $arr['key'] . '%\'';
		}
		$sql=$sql . $real .' ORDER BY mid DESC';
		$sql2=$sql2 . $real;
		if (isset($arr['nowpage'])){
			$num1=$pagenum*($arr['nowpage']-1);
		}else{
			$num1=0;
		}
		$sql.=' LIMIT '.$num1.','.$pagenum;
		$query=$this->db->query($sql);
		$query2=$this->db->query($sql2);
		$temp=$query2->row_array();
		$return['data']=$query->result_array();
		$return['nowpage']=$arr['nowpage'];
		$return['allcount']=$temp['a'];
		return $return;
	}
	
	//获得任务的回复列表
	public function returnlist($arr=''){
		$return=$this->rexGetMReturn();
		$this->db->select('*');
		$this->db->where('mid',$arr['mid']);
		$this->db->order_by('datemake', 'ASC');
		$query=$this->db->get('missionreturn');
		//die($this->db->last_query());
		$return['data']=$query->result_array();
		return $return;
	}
}