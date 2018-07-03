//公用配置文件
//调试开关
"use strict";
//相关配置
var CFG={
	ver: "Ver1.0",
	UM:1,
	UL:2,
	UU:3,
	UD:4,
	pagesize: 20,  //分页数量
};
var URL={  //本站URL地址
	loginin:'/index.php/login/loginin/',
	loginout:'/index.php/loginout/',
	home:'/index.php/home/',
	user:'/index.php/general/user', //HTTP用户列表页
	useradd:'/index.php/general/useradd', //AJAX用户添加
	useredit:'/index.php/general/useredit',  //AJAX用户资料修改
	userdrop:'/index.php/general/userdrop',  //AJAX用户删除
	department:'/rextest/department',
	job:'/rextest/job',
	overview:'/rextest/overview',
	mission:'/rextest/mission',
	missionlist:'/rextest/missionlist',
	missionadd:'/rextest/missionadd',
	returnlist:'/rextest/returnlist',
	consult:'/rextest/consult',
	consultlist:'/rextest/consultlist',
	consultadd:'/rextest/consultadd',
	consultreturn:'/rextest/consultreturn'
};
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