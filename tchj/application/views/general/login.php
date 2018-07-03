<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>太仓海警支队--用户登录</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.2.css"/>
	<link rel="stylesheet" href="/style/login.css"/>
</head>
<body>
	<div id="app" class="outFrame bkStyle1">
		<div class="titles">
			<h1>太仓海警支队</h1>  
			<h2>内部任务管理系统 <span class="ver">{{ver}}</span></h2>
		</div>
		<div class="content">
			<div class="loginForm t3">
				<div class="tipMessage t2">
					<span class="fa" v-bind:class="chk[0].flag"> {{chk[0].msg}}</span>
				</div>
				<div class="rexField fa fa-user fa-lg sBlk" v-bind:class="{'warning':chk[0].obj=='username' || chk[0].obj=='all'}">
					<input type="text" class="rexInput" placeholder="用户名" v-model.trim="login.username" maxlength="30" v-bind:disabled="load" v-on:keyup.13="nextInput(1)"/>
				</div>
				<div class="rexField fa fa fa-lock fa-lg sBlk" v-bind:class="{'warning':chk[0].obj=='password' || chk[0].obj=='all'}">
					<input type="password" class="rexInput" placeholder="登录密码" v-model.trim="login.password" maxlength="30" v-bind:disabled="load" v-on:keyup.13="doLogin"/>
				</div>
				<div class="rexCheck t2">
					<input type="checkbox" v-model="login.auto" v-bind:disabled="load" value="1"/><label>&ensp;下次自动登录</label>
				</div>
				<div class="buttonBar tCT">
					<button class="rexButton infor" @click="doLogin()" v-bind:disabled="load">登&emsp;录</button>
				</div>
			</div>
		</div>
	</div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/md5.min.js"></script>
	<script src="/script/config.js"></script>
	<script src="/script/login.js"></script>
</body>
</html>