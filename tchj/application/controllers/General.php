<?php
//基本数据管理controller
class General extends MY_Controller{
	public function __construct(){
    parent::__construct();
  }
	
	public function user(){   //用户列表页面
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			$data['ERROR']=$result;
			$this->load->view('general/error.php',$data);
		}else{
			$this->load->model('User');
			$result=$this->User->user();
			$data['user']=$result['data'];
			$data['account']=$_SESSION['user'];
			$this->load->model('Base');
			$result=$this->Base->department();
			$data['department']=$result['data'];
			$result=$this->Base->job();
			$data['job']=$result['data'];
			$result=$this->Base->usertypeInfor();
			$data['usertype']=$result['data'];
			$this->load->view('general/userlist.php',$data);
		}
	}
	
	public function useradd(){  //添加新用户
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			rexAjaxReturn(403,'当前用户非管理员，无法添加用户');
			return;
		}
		$indata=array(
			'tid'=>'',
			'username'=>'',
			'password'=>'',
			'pid'=>'',
			'jid'=>'',
			'realname'=>'',
			'telnumber'=>''
		);
		foreach ($indata as $key=>$value){
			$indata[$key]=$this->input->post($key);
		}
		//NOTICE 检测参数完整性
		if (empty($indata['username']) || strlen($indata['password'])!=32 || empty($indata['tid'])){
			$this->_getReturn(401,'添加用户的必要信息缺失');
			return;
		}
		if (!in_array($indata['tid'],array(USERM,USERL,USERU,USERD))){
			$this->_getReturn(401,'用户类型参数错误');
			return;
		}
		if ($indata['tid']<USERD){
			$indata['jid']=0;
		}else{
			if (empty($indata['jid'])){
				$this->_getReturn(401,'添加用户的必要信息缺失');
				return;
			}
		}
		if ($indata['tid']<USERU){
			$indata['pid']=0;
		}else{
			if (empty($indata['pid'])){
				$this->_getReturn(401,'添加用户的必要信息缺失');
				return;
			}
		}
		$this->load->model('User');
		$result=$this->User->useradd($indata);
		rexAjaxReturn($result);
	}
	
	public function useredit(){  //用户资料修改
		$indata=array(
			'uid'=>'',
			'oldpassword'=>'',
			'newpassword'=>'',
			'realname'=>'',
			'telnumber'=>''
		);
		foreach ($indata as $key=>$value){
			$indata[$key]=$this->input->post($key);
		}
		if (empty($indata['uid'])){
			rexAjaxReturn(401,'用户信息缺失，无法进行修改');
			return;
		}
		$result=parent::checkPower([],'accedit',array('uid'=>$indata['uid']));
		if ($result['code']){
			rexAjaxReturn($result);
			return;
		}
		if (!empty($indata['newpassword']) && strlen($indata['newpassword'])!=32){
			rexAjaxReturn(401,'新密码参数错误');
			return;
		}
		$user=$_SESSION['user'];
		if ($user['tid']==USERM){
			$indata['manager']=true;
		}else{
			$indata['manager']=false;
			if (empty($indata['oldpassword']) || strlen($indata['oldpassword'])!=32){
				rexAjaxReturn(401,'原始密码参数错误');
				return;
			}
		}
		$this->load->model('User');
		$result=$this->User->useredit($indata);
		rexAjaxReturn($result);
	}
	
	public function userdrop(){  //删除用户
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			rexAjaxReturn(403,'当前用户非管理员，无法添加用户');
			return;
		}
		$indata=array(
			'uid'=>$this->input->post('uid')
		);
		if ($indata['uid']==$_SESSION['user']['uid']){
			rexAjaxReturn(401,'无法删除当前登录的用户');
			return;
		}
		$this->load->model('User');
		$result=$this->User->useredit($indata);
		rexAjaxReturn($result);
	}
}