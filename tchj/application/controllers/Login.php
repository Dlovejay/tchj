<?php
class Login extends CI_Controller{
	private $userLogin=false;    //标志用户登录状态
	
	public function __construct(){
			parent::__construct();
			$this->load->helper('cookie');
			$this->load->library('session');
			$this->checkLoginStatus();
	}
		
	//检查用户登录状态
	private function checkLoginStatus(){
		if (isset($_SESSION['expiretime'])){
			//检验SESSION是否超时
			if ($_SESSION['expiretime']>time()){
				if (isset($_SESSION['user'])){
					$this->userLogin=true;
					return;
				}
			}
		}else{
			//判断是否自动登录
			if (isset($_COOKIE['autologin'])){
				$autoStr=$_COOKIE['autologin'];
				$username=substr($autoStr,32);
				$password=substr($autoStr,0,32);
				$this->load->model('User');
				$result=$this->User->userlogin($username,$password);
				if ($result['code']){
				}else{
					$this->userLogin=true;
					$this->session->set_userdata(array('user'=>$result['data']));
					$this->session->set_userdata('expiretime',time()+REXSESSIONLIFE);
					setcookie('autologin',$password.$username,time()+REXSESSIONLIFE,'/');
					return;
				}
			}
		}
		unset($_SESSION['user']);
		unset($_SESSION['expiretime']);
		setcookie('autologin','',time()-100,'/');
	}
	
	//HTTP显示登录页面
	public function index(){
		$data['base_url'] = $this->config->item('base_url');
		if ($this->userLogin==false){
			$this->load->view('general/login.php',$data);
		}else{
			$data['account'] = $this->session->userdata('user');
			//$this->load->view('general/main.php',$data);
			redirect(base_url('home/'));
		}
	}

  //AJAX登录验证
	public function loginin(){
		$indata=array(
			'username'=>'',
			'password'=>'',
			'auto'=>''
		);
		foreach ($indata as $key=>$value){
			$indata[$key]=$this->input->post($key);
		}
		//NOTICE 检查参数规范
		if (empty($indata['username']) || empty($indata['password'])){
			rexAjaxReturn('401','登录信息缺失');
			exit();
		}
		if (strlen($indata['password'])!=32){
			rexAjaxReturn('401','登录密码填写错误');
			exit();
		}
		//加载用户model
		$this->load->model('User');
		$result=$this->User->userlogin($indata['username'],$indata['password']);
		if ($result['code']){
			rexAjaxReturn($result);
		}else{
			# 登录成功 种session
			$this->session->set_userdata(array('user'=>$result['data']));
			$this->session->set_userdata('expiretime',time()+REXSESSIONLIFE);
			if ($indata['auto']=='1'){
				setcookie('autologin',$indata['password'].$indata['username'],time()+REXSESSIONLIFE,'/');
			}else{
				setcookie('autologin','',time()-100,'/');
			}
			rexAjaxReturn();
		}
	}
}