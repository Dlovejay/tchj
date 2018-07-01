<?php
//主框架页面controller
class Home extends MY_Controller{
	public function __construct(){
			parent::__construct();
	}
	
	public function index(){
		$data['account']=$_SESSION['user'];
		$this->load->view('general/main.php',$data);
	}
}