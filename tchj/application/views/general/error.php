<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title> 江苏海警支队--系统提示信息</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
<style type="text/css">
body{ display:flex; background-color:#eee;}
.outFrame{ margin:auto; width:auto; height:auto; border:5px solid #282f5d; padding:10px; border-radius:10px; background-color:#fff; box-shadow:0 0 10px rgba(0,0,0,.6);}
.fa{ font-size:30px; vertical-align:middle; position:relative; top:-2px; color:#f90;}
</style>
</head>
<body>
	<div id="app" class="outFrame">
		<span class="fa fa-info-circle"></span>
<?php
	if ($ERROR['code']=='403' || $ERROR['code']=='401'){
		echo $ERROR['message'];
	}else if($ERROR['code']=='440'){
		echo '没有检测到登录状态，请点击&ensp;<a class="rexButton infor" href="'. $config['URLSTR'] .'pages" target="_parent">重新登录</a>&ensp;返回登录页面';
	}
?>
	</div>
</body>
</html>