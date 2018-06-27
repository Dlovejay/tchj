<?php

class Login extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->library('session');
    }

    public function index(){
        $data['base_url'] = $this->config->item('base_url');
        if ($this->userLogin==false){
            $this->load->view('pages/login.php',$data);
        }else{
            $data['user']=rexGetSession('user');
            $this->load->view('pages/main.php',$data);
        }
    }

    //登录
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
        if (empty($indata['password']) or empty($indata['username'])){
            echo json_encode(array('code' => 401, 'message' => '密码输入有误'));
            exit();
        }

        $return = rexGetMReturn();
        //加载用户model
        $this->load->model('User');
        $result=$this->User->userlogin($indata['username'],$indata['password']);
        if (count($result) === 0){
            $return['code']=500;
            $return['message']='用户名或者密码错误';
        }
        # 登录成功 种session
        $this->session->set_userdata(array('user_info' => $result[0]));
        echo json_encode($return);
    }
}