<?php
class Loginout extends CI_Controller{
	public function __construct(){
			parent::__construct();
			$this->load->helper('cookie');
			$this->load->library('session');
	}
	
	//HTTP登出页面及跳转
	public function index(){
		unset($_SESSION['user']);
		unset($_SESSION['expiretime']);
		setcookie('autologin','',time()-100,'/');
		redirect(base_url('login/'));
	}
}