<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>管理系统主页</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
	<link rel="stylesheet" href="/style/main.css?1/">
</head>
<body>
<div id="app" class="outFrame rexFrame noBottom">
  <div class="rexTopbar">
    <img src="/style/images/logo.png"/>
		<h1 class="c0F">太仓海警支队内部任务管理系统 <span class="ver"></span></h1>
		<div class="userInfor">
			<div class="staticPart fa fa-caret-down">
				<span class="fa fa-user face"></span>
				<span class="userName t3 c0F"><?php echo $user['username']; ?></span>
				<span class="userType"><?php echo $user['tname']; ?></span>
			</div>
			<div class="userMenu"><a class="rexButton sBlk" id="myaccount" href="<?php echo $config['URLSTR'] . 'pages/account/edit'; ?>" target="contentshow">我的账户</a></div>
			<div class="userMenu"><a class="rexButton sBlk" href="<?php echo $config['URLSTR'] . 'pages/loginout'; ?>">退出登录</a></div>
		</div>
  </div>
  <nav class="rexLeftpart t3">
<?php
if ($user['tid']==$config['USERM']){ 
?>
		<a class="fa fa-list-alt" href="<?php echo $config['URLSTR'] . 'pages/department/'; ?>" target="contentshow">&ensp;部门管理</a>
		<a class="fa fa-group" href="<?php echo $config['URLSTR'] . 'pages/job/'; ?>" target="contentshow">&ensp;职务管理</a>
		<a class="fa fa-user" href="<?php echo $config['URLSTR'] . 'pages/userlist/'; ?>" target="contentshow">&ensp;用户管理</a>
<?php
}
?>
		<a class="splitBar fa fa-calendar" href="<?php echo $config['URLSTR'] . 'pages/mission/'; ?>" target="contentshow">&ensp;任务列表</a>
		<a class="fa fa-file-o" href="" target="contentshow">&ensp;请示列表</a>
  </nav>
	<div class="rexRightpart">
		<iframe src="" name="contentshow"></iframe>
	</div>
</div>
<div id="URL" class="dataField"><?php echo $config['URLSTR']; ?></div>
<div id="user" class="dataField"><?php echo json_encode($user); ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/config.js"></script>
<script>
$(function(){
	var temp=$('.rexLeftpart>a');
	//菜单栏的
	$(temp).click(function(){
		clearStatus();
		$(this).addClass('sel');
	});
	//我的账户
	$("#myaccount").click(function(){
		clearStatus();
	});
	function clearStatus(){
		$(temp).removeClass('sel');
	}
	//版本号显示
	$(".ver").text(CFG.ver);
});
</script>
</body>
</html>