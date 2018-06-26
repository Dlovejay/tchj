//公用配置文件
//调试开关
"use strict";
//var rexBug=true;
//var rexLocal=false;
//相关配置
var CFG={
	//server: "http://127.0.0.1/index.php/",
	ver: "Ver1.0",
	cut: 20  //分页数量
};

/*
if (rexLocal){
	CFG.server="/server/"
}
*/
//正则字典
var REG={
	uname:/^\w{3,30}$/, //用户名
	upwd:/^[0-9a-zA-Z][\w-+#@%]{4,30}$/, //密码
	name:/^[a-zA-Z中文]{2,30}$/,  //用户姓名
	tel:/^\d{1,4}[\-\s]?\d{1,4}[\-\s]?\d{3,11}$/,	//联系电话
	pname:/^[a-zA-Z0-9中文]{1,20}$/,  //部门名称
	jname:/^[a-zA-Z0-9中文]{1,20}$/,   //职务名称
	url:/^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z中文]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/ //url地址
};