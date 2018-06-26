<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>太仓海警支队</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
	<link rel="stylesheet" href="/style/login.css"/>
</head>
<body>
	<div id="app" class="outFrame bkStyle1">
		<div class="titles">
			<h1>太仓海警支队</h1>  
			<h2>内部任务管理系统 <span class="ver">{{cfg.ver}}</span></h2>
		</div>
		<div class="content">
			<div class="loginForm t3">
				<div class="tipMessage t1" :class="mtype">
					<span class="fa" :class="tipClass">&ensp;{{message}}</span>
				</div>
				<div class="rexField fa fa-user fa-lg sBlk" :class="{warning:regDic.uname.chk}">
					<input type="text" class="rexInput" placeholder="用户名" v-model.trim.lazy="uname" maxlength="30" :disabled="login" v-on:keyup.13="nextInput(1)"/>
				</div>
				<div class="rexField fa fa fa-lock fa-lg sBlk" :class="{warning:regDic.upwd.chk}">
					<input type="password" class="rexInput" placeholder="登录密码" v-model.trim.lazy="upwd" maxlength="30" :disabled="login" v-on:keyup.13="doLogin"/>
				</div>
				<div class="rexCheck t2">
					<input type="checkbox" v-model="auto" :disabled="login"/><label>&ensp;下次自动登录</label>
				</div>
				<div class="buttonBar tCT">
					<button class="rexButton infor" @click="doLogin" :disabled="login">登&emsp;录</button>
				</div>
			</div>
		</div>
	</div>
	<div id="URL" class="dataField"><?php echo $base_url; ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/md5.min.js"></script>
	<script src="/script/config.js"></script>
	<script src="/script/login.js"></script>
</body>
</html>