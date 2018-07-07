<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>未登录提示页面</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.2.css"/>
<style type="text/css">
body{ background-color:#fff;}
.outFrame{ display:flex;}
.outFrame>*{ margin:auto;}
</style>
</head>
<body>
<div id="app" class="outFrame tCT">
	<div>
		<span class="rexLabel">您当前还未登录任务管理系统！</span><br/>
		<a id="login" class="rexButton infor fa fa-user" href="" target="_blank"> 登录</a>
		<button class="rexButton fa fa-refresh" onclick="location.reload()"> 刷新</button>
	</div>
</div>
<script src="/script/config.js"></script>
<script>
var loginButton=document.getElementById('login');
loginButton.setAttribute('href',URL.login);
</script>
</body>
</html>