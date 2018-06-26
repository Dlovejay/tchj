<?php

class MY_Controller extends CI_Controller
{
    private $userLogin=false;    //标志用户登录状态
    //ajax返回的错误标志 403权限问题 401客户端数据问题 440未登录 500服务端错误...

    public function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->check_login();
    }

    protected function check_login()
    {
        $user_info = $this->session->userdata('user_info');
        if (empty($user_info)){
            redirect(base_url('Page/index'));
        }
    }
}