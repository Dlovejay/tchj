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
	loginin:'/index.php/rextest/loginin/',
	loginout:'/index.php/rextest/loginout/',
	main:'/index.php/rextest/',
	department:'/index.php/rextest/department',
	job:'/index.php/rextest/job',
	user:'/index.php/rextest/user',
	mission:'/index.php/rextest/mission',
	missionlist:'/index.php/rextest/missionlist',
	missionadd:'/index.php/rextest/missionadd',
	returnlist:'/index.php/rextest/returnlist',
	consult:'/index.php/rextest/consult',
	consultlist:'/index.php/rextest/consultlist',
	consultadd:'/index.php/rextest/consultadd',
	consultreturn:'/index.php/rextest/consultreturn'
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