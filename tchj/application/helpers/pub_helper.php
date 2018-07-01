<?php
//本站公用函数
defined('BASEPATH') OR exit('No direct script access allowed');

//生成应对ajax的返回对象并输出
//ajax返回的错误标志 401客户端数据问题 403权限问题 440未登录 500服务端错误...
function rexAjaxReturn($code=0,$message='',$data=''){
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

//model返回controller的数据一般格式，用于ajax的服务端返回
//model error code 550_SQL语句 503_权限相关 500正常抛错
function rexGetMReturn(){
	return array('code'=>0,'message'=>'','data'=>'');
}