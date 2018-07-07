<?php 
//前端测试用 用于嵌入网站系统
class Notice extends CI_Controller{
	private $userLogin=false;    //标志用户登录状态
	
	public function __construct(){
			parent::__construct();
			$this->load->helper('cookie');
			$this->load->library('session');
			$this->checkLoginStatus();
	}
		
	//检查用户登录状态
	private function checkLoginStatus(){
		if (isset($_SESSION['user'])){
			$this->userLogin=true;
			return;
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
					setcookie('autologin',$password.$username,time()+REXSESSIONLIFE,'/');
					return;
				}
			}
		}
		unset($_SESSION['user']);
		setcookie('autologin','',time()-100,'/');
	}
	
	function index(){  //登录页或者主页
		if ($this->userLogin==false){
			$this->load->view('plugin/noticelogin.php');
		}else{
			$user=$_SESSION['user'];
      $this->load->model('TaskModel');
      $overview=$this->TaskModel->GetStatistics($user);
			$data['user']=$user;
			$data['overview']=$overview;
			$this->load->view('plugin/noticeshow.php',$data);
		}
	}
}