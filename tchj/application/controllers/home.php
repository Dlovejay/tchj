<?php
//主框架页面controller
class Home extends MY_Controller{
	public function __construct(){
			parent::__construct();
	}
	
	public function index(){
		$user=$_SESSION['user'];
		$indata=array('uid'=>$user['uid']);
		$this->load->model('User');
		$result=$this->User->user($indata);
		$data['account']=$result['data'][0];
		$this->load->view('general/main.php',$data);
	}
}