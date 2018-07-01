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
<div id="app" class="outFrame rexFrame nb">
  <div class="rexTopbar">
    <img src="/style/images/logo.png"/>
		<h1 class="c0F">太仓海警支队内部任务管理系统 <span class="ver"></span></h1>
		<div class="userInfor">
			<div class="staticPart fa fa-caret-down">
				<span class="fa fa-user face"></span>
				<span class="userName t3 c0F">{{me.username}}</span>
				<span class="userType">{{me.tname}}</span>
			</div>
			<div class="userMenu"><button class="rexButton sBlk" @click="">我的账户</button></div>
			<div class="userMenu"><a class="rexButton sBlk" v-bind:href="url.loginout">退出登录</a></div>
		</div>
  </div>
  <nav class="rexLeftpart t3">
		<a class="fa fa-sitemap" v-bind:href="url.department" target="contentshow" v-bind:class="{'sel':nowSel==1}" @click="setSel(1)">&ensp;部门管理</a>
		<a class="fa fa-list-alt" v-bind:href="url.job" target="contentshow" v-bind:class="{'sel':nowSel==2}" @click="setSel(2)">&ensp;职务管理</a>
		<a class="fa fa-group" v-bind:href="url.user" target="contentshow" v-bind:class="{'sel':nowSel==3}" @click="setSel(3)">&ensp;用户管理</a>
		<a class="splitBar fa fa-bar-chart-o" v-bind:href="url.overview" target="contentshow" v-bind:class="{'sel':nowSel==4}" @click="setSel(4)">&ensp;工作概览</a>
		<a class="fa fa-calendar" v-bind:href="url.mission" target="contentshow" v-bind:class="{'sel':nowSel==5}" @click="setSel(5)">&ensp;任务列表</a>
		<a class="fa fa-paste" v-bind:href="url.consult" target="contentshow" v-bind:class="{'sel':nowSel==6}" @click="setSel(6)">&ensp;请示列表</a>
  </nav>
	<div class="rexRightpart">
		<iframe v-bind:src="url.overview" name="contentshow"></iframe>
	</div>
	
	
</div>
<div id="account" class="dataField"><?php echo json_encode($account); ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/config.js"></script>
	<script src="/script/main.js"></script>
</body>
</html>