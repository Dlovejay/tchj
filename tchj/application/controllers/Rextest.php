<?php 
//前端测试用
class Rextest extends CI_Controller{
	private $userLogin=false;
	public function __construct(){
		parent::__construct();
		$this->load->helper('cookie');
		$this->load->library('session');
		
		$this->_chkUser();
	}
	
	//检查用户登录状态
	private function _chkUser(){
		if (isset($_SESSION['user'])){
			$this->userLogin=true;
		}else{
			//判断是否自动登录
			if (isset($_COOKIE['autologin'])){
				$autoStr=$_COOKIE['autologin'];
				$username=substr($autoStr,32);
				$password=substr($autoStr,0,32);
				
				$result=$this->_chkLogin($username);
				if ($result==false){
					setcookie('autologin','',time()-3600,'/');
				}else{
					$this->userLogin=true;
					setcookie('autologin',md5('123123').$username, time()+3600,'/');
					$_SESSION['user']=$result;
				}
			}
		}
	}
	
	//检验登录
	private function _chkLogin($username){
		switch($username){
			case 'admin':
				$uid=1;
				$tid=1;
				$pid=0;
				$tname='系统管理员';
				break;
			case 'user':
				$uid=2;
				$tid=4;
				$pid=6;
				$tname='下级用户';
				break;
			case 'leader':
				$uid=5;
				$tid=2;
				$pid=0;
				$tname='支队领导';
				break;
			case 'manager':
				$uid=3;
				$tid=3;
				$pid=3;
				$tname='上级用户';
				break;
			default:
				return false;
		}
		return array(
			'username'=>$username,
			'uid'=>$uid,
			'tid'=>$tid,
			'pid'=>$pid,
			'tname'=>$tname
		);
	}
	
	//生成应对ajax的返回对象
	private function _getReturn($code=0,$message='',$data=''){
		$return=array('code'=>0,'message'=>'','data'=>'');
		if (is_array($code)){
			$return=$code;
		}else{
			$return['code']=$code;
			$return['message']=$message;
			$return['data']=$data;
		}
		echo json_encode($return);
	}
	
	function index(){  //登录页或者主页
		if ($this->userLogin==false){
			$this->load->view('rexshow/login.php');
		}else{
			$data['user']=$_SESSION['user'];
			$this->load->view('rexshow/main.php',$data);
		}
	}
	
	//AJAX登录
	function loginin(){
		$indata=array(
			'username'=>'',
			'userpwd'=>'',
			'auto'=>''
		);
		foreach ($indata as $key=>$value){
			$indata[$key]=$this->input->post($key);
		}
		$result=$this->_chkLogin($indata['username']);
		if ($result==false){
			$this->_getReturn(400,'用户名或者密码错误');
		}else{
			$_SESSION['user']=$result;
			if ($indata['auto']=='1'){
				setcookie('autologin',md5('123123').$indata['username'], time()+3600,'/');
			}
			$this->_getReturn();
		}
	}
	//HTTP登出
	function loginout(){
		//清除session和cookie
		unset($_SESSION['user']);
		setcookie('autologin','',time()-3600,'/');
		header('Location:/index.php/rextest/');
	}
	
	//HTTP部门管理
	function department(){
		$this->load->model('Test');
		$result=$this->Test->department();
		$data['department']=$result['data'];
		$this->load->view('rexshow/department.php',$data);
	}
	
	//HTTP职务管理
	function job(){
		$this->load->model('Test');
		$result=$this->Test->job();
		$data['job']=$result['data'];
		$this->load->view('rexshow/job.php',$data);
	}
	
	//HTTP显示用户列表
	public function user(){
		$this->load->model('Test');
		$result=$this->Test->user();
		$data['user']=$result['data'];
		$data['account']=$_SESSION['user'];
		$this->load->view('rexshow/userlist.php',$data);
	}
	
	//HTTP任务列表页面
	public function mission(){
		$this->load->model('Test');
		$result=$this->Test->user();
		$data['user']=$result['data'];
		$result=$this->Test->department();
		$data['department']=$result['data'];
		$data['account']=$_SESSION['user'];
		$this->load->view('rexshow/mission.php',$data);
	}
	
	//AJAX 获取任务列表
	public function missionlist(){
		$this->load->model('Test');
		$result=$this->Test->mission(array('nowpage'=>1));
		$this->_getReturn($result);
	}
	
	//AJAX 获得回复列表
	public function returnlist(){
		$indata=array(
			'mid'=>$this->input->post('mid')
		);
		$this->load->model('Test');
		$result=$this->Test->returnlist($indata);
		$this->_getReturn($result);
	}
	
	//HTTP请示列表页面
	public function consult(){
		$this->load->model('Test');
		$result=$this->Test->user();
		$data['user']=$result['data'];
		$result=$this->Test->department();
		$data['department']=$result['data'];
		$data['account']=$_SESSION['user'];
		$this->load->view('rexshow/consult.php',$data);
	}
	
}