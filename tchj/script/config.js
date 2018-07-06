//公用配置文件
//调试开关
"use strict";
//相关配置
var CFG={
	ver: "Ver1.0",
	FTP: "http://192.168.1.201:21/",
	UM:1,
	UL:2,
	UU:3,
	UD:4,
	pagesize: 20,  //分页数量
};
var URL={  //本站URL地址
	loginin:'/index.php/login/loginin/',  //AJAX用户登录
	loginout:'/index.php/loginout/',      //HTTP用户登出
	home:'/index.php/home/',              //HTTP首页
	user:'/index.php/general/user', //HTTP用户列表页
	useradd:'/index.php/general/useradd', //AJAX用户添加
	useredit:'/index.php/general/useredit',  //AJAX用户资料修改
	userdrop:'/index.php/general/userdrop',  //AJAX用户删除
	department:'/index.php/general/department',  //HTTP部门管理页面
	departmentadd:'/index.php/general/departmentadd',  //AJAX部门添加
	departmentedit:'/index.php/general/departmentedit',  //AJAX部门修改
	departmentdrop:'/index.php/general/departmentdrop',  //AJAX部门删除
	job:'/index.php/general/job',  //HTTP职务管理页面
	jobadd:'/index.php/general/jobadd', //AJAX职务添加
	jobedit:'/index.php/general/jobedit', //AJAX职务修改
	jobdrop:'/index.php/general/jobdrop', //AJAX职务删除
	overview:'/index.php/general/overview',  //概览
	taskover:'/index.php/task/statistics',  //任务统计
	task:'/index.php/task/',   //HTTP任务列表页面
	tasklist:'/index.php/task/lists', //AJAX任务列表
	taskadd:'/index.php/task/add',    //AJAX任务添加
	taskdetail:'/index.php/task/detail', //AJAX获得任务回复列表
	tasknext:'/index.php/task/next',  //AJAX回复任务
	taskedit:'/index.php/task/edit',  //AJAX任务编辑
	consult:'/index.php/consult/',
	consultlist:'/index.php/consult/consult_list',
	consultadd:'/index.php/consult/add_consult',
	consultreply:'/index.php/consult/consult_replies'
};
//正则字典
var REG={
	uname:/^\w{3,30}$/, //用户名
	upwd:/^[0-9a-zA-Z][\w-+#@%]{4,30}$/, //密码
	name:/^[a-zA-Z中文]{2,30}$/,  //用户姓名
	tel:/^\d{1,4}[\-\s]?\d{1,4}[\-\s]?\d{3,11}$/,	//联系电话
	pname:/^[a-zA-Z0-9中文]{1,20}$/,  //部门名称
	jname:/^[a-zA-Z0-9中文]{1,20}$/,   //职务名称
	url:/^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z中文]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/, //url地址
	title:/^[\w中文\s]{1,50}$/
};