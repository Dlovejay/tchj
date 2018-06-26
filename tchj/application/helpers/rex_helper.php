<?php
//本站公用函数
defined('BASEPATH') OR exit('No direct script access allowed');

define('REXSESSIONUSED','user');

//设置session
function rexSetSession($nameStr,$val){
	$_SESSION[$nameStr]=$val;
	$_SESSION['expiretime']=time()+REXSESSIONLIFE;
}

//删除对应的SESSION
function rexClrSession($nameStr=''){
	if ($nameStr!=''){
		unset($_SESSION[$nameStr]);
	}else{
		$item=explode(',',REXSESSIONUSED);
		foreach ($item as $val){
			unset($_SESSION[$val]);
		}
	}
}

//获得对应的session值
function rexGetSession($key,$subkey=''){
	if ($key=='' || isset($_SESSION[$key])==false) return '';
	if ($subkey=='') return $_SESSION[$key];
	if (is_array($_SESSION[$key]) && isset($_SESSION[$key][$subkey])){
		return $_SESSION[$key][$subkey];
	}else{
		return '';
	}
}
//获得本站自定义配置数据
function rexGetConfig(){
	return array(
		'URLSTR'=>URLSTR,
		'FTPSTR'=>FTPSTR,
		'USERM'=>USERM,
		'USERL'=>USERL,
		'USERU'=>USERU,
		'USERD'=>USERD
	);
}
//model返回controller的数据一般格式，用于ajax的服务端返回
//model error code 550_SQL语句 503_权限相关 500正常抛错
function rexGetMReturn(){
	return array('code'=>0,'message'=>'','data'=>'');
}

if (!function_exists('rexChkSession')){
	//自动获取当前对话的expiretime，比对是否超时，超时则清除其他相关session，否则刷新生存周期
	function rexChkSession(){
		$exptime=rexGetSession('expiretime');
		if ($exptime!=''){
			if ($exptime<time()){
				unset($_SESSION['expiretime']);
				rexClrSession();
			}else{
				$_SESSION['expiretime']=time()+REXSESSIONLIFE;  //刷新时间戳
			}
		}else{
			rexClrSession();
		}
	}
}
	