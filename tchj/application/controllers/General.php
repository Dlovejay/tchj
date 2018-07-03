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
		$result=$this->User->userdrop($indata);
		rexAjaxReturn($result);
	}
	
	public function department(){  //部门管理页面
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			$data['ERROR']=$result;
			$this->load->view('general/error.php',$data);
		}else{
			$this->load->model('Base');
			$result=$this->Base->department();
			$data['department']=$result['data'];
			$this->load->view('general/department.php',$data);
		}
	}
	
	public function departmentadd(){  //部门添加
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			rexAjaxReturn(403,'当前用户非管理员，无法添加部门信息');
			return;
		}
		$indata=array(
			'pname'=>'',
			'plevel'=>''
		);
		foreach ($indata as $key=>$value){
			$indata[$key]=$this->input->post($key);
		}
		//NOTICE 检测参数完整性
		if ($indata['plevel']!="1" && $indata['plevel']!="2"){
			rexAjaxReturn(401,'部门级别参数错误');
			return;
		}
		if (empty($indata['pname'])){
			rexAjaxReturn(401,'缺少部门名称信息');
			return;
		}
		$this->load->model('Base');
		$result=$this->Base->departmentOP('add',$indata);
		rexAjaxReturn($result);
	}
	
	public function departmentedit(){   //部门编辑
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			rexAjaxReturn(403,'当前用户非管理员，无法编辑部门信息');
			return;
		}
		$indata=array(
			'pid'=>'',
			'pname'=>''
		);
		foreach ($indata as $key=>$value){
			$indata[$key]=$this->input->post($key);
		}
		//NOTICE 检测参数完整性
		if (empty($indata['pid']) || empty($indata['pname'])){
			rexAjaxReturn(403,'需要修改的部门信息参数缺失');
			return;
		}
		$this->load->model('Base');
		$result=$this->Base->departmentOP('edit',$indata);
		rexAjaxReturn($result);
	}
	
	public function departmentdrop(){  //部门删除
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			rexAjaxReturn(403,'当前用户非管理员，无法删除部门信息');
			return;
		}
		$indata=array(
			'pid'=>$this->input->post('pid')
		);
		//NOTICE 检测参数完整性
		if (empty($indata['pid'])){
			rexAjaxReturn(403,'需要删除的部门信息参数缺失');
			return;
		}
		$this->load->model('Base');
		$result=$this->Base->departmentOP('drop',$indata);
		rexAjaxReturn($result);
	}
	
	public function job(){  //职务管理页面
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			$data['ERROR']=$result;
			$this->load->view('general/error.php',$data);
		}else{
			$this->load->model('Base');
			$result=$this->Base->job();
			$data['job']=$result['data'];
			$result=$this->Base->department(array('plevel'=>1));
			$data['department']=$result['data'];
			$this->load->view('general/job.php',$data);
		}
	}
	
	public function jobadd(){  //职务添加
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			rexAjaxReturn(403,'当前用户非管理员，无法添加职务信息');
			return;
		}
		$indata=array(
			'pid'=>'',
			'jname'=>''
		);
		foreach ($indata as $key=>$value){
			$indata[$key]=$this->input->post($key);
		}
		//NOTICE 检测参数完整性
		if (empty($indata['pid']) || empty($indata['jname'])){
			rexAjaxReturn(401,'职务信息不完整');
			return;
		}
		$this->load->model('Base');
		$result=$this->Base->jobOP('add',$indata);
		rexAjaxReturn($result);
	}
	
	public function jobedit(){   //职务编辑
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			rexAjaxReturn(403,'当前用户非管理员，无法编辑职务信息');
			return;
		}
		$indata=array(
			'jid'=>'',
			'pid'=>'',
			'jname'=>''
		);
		foreach ($indata as $key=>$value){
			$indata[$key]=$this->input->post($key);
		}
		//NOTICE 检测参数完整性
		if (empty($indata['jid']) || empty($indata['pid']) || empty($indata['jname'])){
			rexAjaxReturn(403,'需要修改的职务信息参数缺失');
			return;
		}
		$this->load->model('Base');
		$result=$this->Base->jobOP('edit',$indata);
		rexAjaxReturn($result);
	}
	
	public function jobdrop(){  //职务删除
		$result=parent::checkPower([USERM]);
		if ($result['code']){
			rexAjaxReturn(403,'当前用户非管理员，无法删除职务信息');
			return;
		}
		$indata=array(
			'jid'=>$this->input->post('jid')
		);
		//NOTICE 检测参数完整性
		if (empty($indata['jid'])){
			rexAjaxReturn(403,'需要删除的职务信息参数缺失');
			return;
		}
		$this->load->model('Base');
		$result=$this->Base->jobOP('drop',$indata);
		rexAjaxReturn($result);
	}
}