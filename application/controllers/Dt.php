<?php
//model部件测试用
class Dt extends CI_Controller{
	private $show='pages/check.php';
	
	function login($username,$upwd){
		if ($username=='' || $upwd==''){
			echo '登录信息缺失，请附带用户名和密码';
		}else{
			$upwd=md5($upwd);
			$this->load->model('User');
			$result=$this->User->userlogin($username,$upwd);
			if ($result['code']){
				echo $result['code'].' '.$result['message'];
			}else{
				echo '用户'.$username.'登录成功';
			}
		}
	}
	
	function user($id=''){  //获得用户列表
		if ($id!=''){
			$indata=array('uid'=>$id);
		}else{
			$indata='';
		}
		$this->load->model('User');
		$result=$this->User->user($indata);
		if ($result['code']){
			echo $result['code'].' '.$result['message'];
		}else{
			$data['data']=$result['data'];
			$this->load->view($this->show,$data);
		}
	}
	
	function useradd(){  //添加用户
		$indata=array(
			'username'=>'kalakala',
			'userpwd'=>'anlgmsiu12ngux987rn5lx098643nrsg',
			'tid'=>'4',
			'pid'=>'9',
			'jid'=>'3',
			'realname'=>'卡拉拉',
			'telnumber'=>''
		);
		$this->load->model('User');
		$result=$this->User->useradd($indata);
		if ($result['code']){
			echo $result['code'].' '.$result['message'];
		}else{
			echo '用户添加成功，新插入用户的uid为'.$result['data'];
		}
	}
	
	function useredit($id=''){  //修改用户
		if ($id==''){
			echo '请指定一个需要修改的用户id编号，测试修改将只会修改他的姓名和电话';
			return;
		}
		$indata=array(
			'uid'=>$id,
			'realname'=>'卡拉拉',
			'telnumber'=>'123123123'
		);
		$this->load->model('User');
		$result=$this->User->useredit($indata);
		if ($result['code']){
			echo $result['code'].' '.$result['message'];
		}else{
			echo '用户资料修改成功';
		}
	}
}