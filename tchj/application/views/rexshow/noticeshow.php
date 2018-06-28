<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>未登录提示页面</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
<style type="text/css">
body{ background-color:#fff;}
.outFrame{ display:flex;}
.outFrame>*{ margin:auto;}
.lay2col{ min-width:200px;}
.lay2col li{min-width:80px;}
.title{ border-bottom:1px solid #ccc; padding-bottom:5px; text-align:center;}
ul li{ padding:2px;}
.lay2col{ margin-top:5px; margin-bottom:5px;}
</style>
</head>
<body>
<div id="app" class="outFrame">
	<div>
		<div class="title">欢迎，<strong><?php echo $user['username']; ?></strong> 需要处理事务有</div>
		<ul class="lay2col noHead">
			<li>任务评审 <strong>1</strong></li>
			<li>请示事务 <strong>1</strong></li>
			<li>任务事务 <strong>2</strong></li>
		</ul>
		<div class="tCT">
			<a class="rexButton infor t1" href="http://192.168.1.32/index.php/rextest/" target="_blank">返回任务管理系统</a>
			<a class="rexButton t1 fa fa-refresh" href=""> 刷新</a>
		</div>
	</div>
</div>
<script>
setTimeout(function(){
	location.reload();
},60000*5);
</script>
</body>
</html>