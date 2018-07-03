<?php
//部门信息，职务信息，用户类别的操作
class Base extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	//查询部门信息
	public function department($arr=''){
		$return=rexGetMReturn();
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
		$return=rexGetMReturn();
		switch($op){
			case 'add':   //增加部门信息
				//检查名称是否重复
				$this->db->select('pid');
				$this->db->where('pname',$arr['pname']);
				$this->db->from('department');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='已经存在该名称的部门';
					return $return;
				}
				//插入数据
				$row=array(
					'pname'=>$arr['pname'],
					'plevel'=>$arr['plevel'],
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
				$this->db->where('pname',$arr['pname']);
				$this->db->from('department');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='已经存在该名称的部门';
					return $return;
				};
				$row=array(
					'pname'=>$arr['pname']
				);
				$this->db->where('pid',$arr['pid']);
				$query=$this->db->update('department',$row);
				if (!$query){
					$return['code']=550;
					$return['message']='部门信息修改失败，请工程师核查';
				}else if($this->db->affected_rows()==0){
					$return['code']=500;
					$return['message']='部门信息没有被修改';
				}
				return $return;
			case 'drop':  //删除部门信息
				//检查当前部门是否已经有用户存在
				$this->db->select('uid');
				$this->db->where('pid',$arr['pid']);
				$this->db->from('user');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='该部门下已经存在用户信息，请确保要操作的部门无对应用户信息后再删除';
					return $return;
				};
				//检查当前任务权限已经含有当前部门，有则目前也不能删除
				$this->db->where('pid',$arr['pid']);
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
		$return=rexGetMReturn();
		if (is_array($arr) && count($arr)>0){
			$this->db->select('*');
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
			$query=$this->db->get('job');
			if (!$query){
				$return['code']=550;
				$return['message']='职务查询部件错误，请工程师核查';
				return $return;
			}
			$return['data']=$query->result_array();
		}else{
			$this->db->select('*');
			$this->db->order_by('jid','ASC');
			$query=$this->db->get('job');
			$return['data']=$query->result_array();
		}
		return $return;
	}
	//编辑职务信息
	public function jobOP($op,$arr){
		$return=rexGetMReturn();
		switch($op){
			case 'add':   //增加职务信息
				//检查对应的部门信息是否存在
				$this->db->select('pid');
				$this->db->where('pid',$arr['pid']);
				$this->db->where('plevel',1);
				$this->db->from('department');
				if ($this->db->count_all_results()<=0){
					$return['code']=401;
					$return['message']='职务对应的部门参数错误';
					return $return;
				};
				//检查同个部门下是否有重名职务
				$this->db->select('jid');
				$this->db->where('jname',$arr['jname']);
				$this->db->where('pid',$arr['pid']);
				$this->db->from('job');
				if ($this->db->count_all_results()>0){
					$return['code']=401;
					$return['message']='在该部门下已经存在该名称的职务信息';
					return $return;
				};
				//插入数据
				$row=array(
					'jname'=>$arr['jname'],
					'pid'=>$arr['pid'],
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
				$this->db->where('pid',$arr['pid']);
				$this->db->where('plevel',1);
				$this->db->from('department');
				if ($this->db->count_all_results()<=0){
					$return['code']=500;
					$return['message']='职务对应的部门参数错误';
					return $return;
				}
				//检查名称是否重复
				$this->db->select('jid');
				$this->db->where('jname',$arr['jname']);
				$this->db->where('pid',$arr['pid']);
				$this->db->from('job');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='在该部门下已经存在该名称的职务信息';
					return $return;
				};
				$row=array(
					'jname'=>$arr['jname'],
					'pid'=>$arr['pid'],
				);
				$this->db->where('jid',$arr['jid']);
				$query=$this->db->update('job',$row);
				if (!$query){
					$return['code']=550;
					$return['message']='职务信息修改失败，请工程师核查';
				}else if($this->db->affected_rows()==0){
					$return['code']=500;
					$return['message']='职务信息没有被修改';
				}
				return $return;
			case 'drop':  //删除职务信息
				//检查当前部门是否已经有用户存在
				$this->db->select('uid');
				$this->db->where('jid',$arr['jid']);
				$this->db->from('user');
				if ($this->db->count_all_results()>0){
					$return['code']=500;
					$return['message']='该职务下已经存在用户信息，请确保要操作的职务无对应用户信息后再删除';
					return $return;
				};
				$this->db->where('jid',$arr['jid']);
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
		$return=rexGetMReturn();
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
}