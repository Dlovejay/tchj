<?php

class MY_Controller extends CI_Controller
{
    private $userLogin=false;    //标志用户登录状态
    protected $limit = 3;      //分页每页显示条数
    //ajax返回的错误标志 403权限问题 401客户端数据问题 440未登录 500服务端错误...

    public function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->check_login();
    }

    protected function check_login()
    {
        $user_info = $this->session->userdata('user');
        if (empty($user_info)){
            redirect(base_url('login/index'));
        }
    }

    //检查当前操作的权限
    //$usertype 用户类型常量数组 如果为空则不需要验证
    //$op 操作参数，用于检测某些特例情况
    //$arr 检测某些特殊情况可能用到的数据对象
    protected function _chkPower($usertype=[],$op='',$arr=array()){
        $return=array(
            'code'=>'',
            'message'=>''
        );

        $user = $this->session->userdata('user');
        if (count($usertype)>0){
            if (in_array($user['tid'],$usertype)==false){
                $return['code']='403';
                $return['message']='当前用户没有权限进行该操作';
                return $return;
            }
        }
        if ($op=='') return $return;
        $tempArray=explode(',',$op);
        for ($i=0; $i<count($tempArray); $i++){
            switch($op){
                case 'ajax':
                    //判定页面强制ajax访问
                    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest'){
                        echo '当前页面访问不正确';
                        die();
                    }
                    break;
                case 'accedit':
                    //检查用户修改操作 非管理员用户只能修改自己的账户信息
                    if ($user['tid']!=USERM && $user['uid']!=$arr['uid']){
                        $return['code']='403';
                        $return['message']='当前账户没有权限修改其他用户的资料';
                        return $return;
                    }
                    break;
                case 'accstop':
                    //禁用用户 无法禁用自己的账户
                    if ($user['uid']==$arr['uid']){
                        $return['code']='401';
                        $return['message']='无法禁用自己的账户';
                        return $return;
                    }
                    break;
            }
        }
        return $return;
    }
}