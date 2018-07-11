<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>任务信息统计</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.2.css"/>
<style type="text/css">
body{ background-color:#fff;}
.dataField{ display:none;}
.outFrame{ display:flex;}
.outFrame>*{ margin:auto;}
.lay2col{ min-width:200px;}
.lay2col li{min-width:80px;}
.title{ border-bottom:1px solid #ccc; padding-bottom:5px; text-align:center;}
ul li{ padding:2px; text-align:center;}
.lay2col{ margin-top:5px; margin-bottom:5px;}
</style>
</head>
<body>
<div id="app" class="outFrame">
	<div>
		<div class="title">欢迎，<strong><?php echo $user['username']; ?></strong> <span id="tips">当前任务如下</span></div>
		<ul id="tongji" class="lay2col noHead">
			<li>进行中 <strong id="doing"></strong></li>
			<li>已完成 <strong id="finished"></strong></li>
			<li>超&emsp;时 <strong id="timeout"></strong></li>
		</ul>
		<div class="tCT">
			<a id="backButton" class="rexButton infor t1" href="http://192.168.1.32/index.php/rextest/" target="_blank">返回任务管理系统</a>
			<a id="refbutton" class="rexButton t1 fa fa-refresh" href=""> 刷新</a>
		</div>
	</div>
	<div id="user" class="dataField"><?php echo json_encode($user); ?></div>
	<div id="overview" class="dataField"><?php echo json_encode($overview); ?></div>
</div>
<script src="/script/relax_function1.1.1.js"></script>
<script src="/script/config.js"></script>
<script>
var me=JSON.parse(document.getElementById('user').innerText);
var tips=document.getElementById('tips');
var tj=document.getElementById('tongji');
var backButton=document.getElementById('backButton');
var refbutton=document.getElementById('refbutton');
backButton.setAttribute('href',URL.home);
if (me.tid=='1' || me.tid=='2'){
	tips.style.display='none';
	tj.style.display='none';
	refbutton.style.display='none';
	backButton.parentNode.className='tCT sP1';
}else{
	setTimeout(function(){
		location.reload();
	},60000*5);
	var overview=JSON.parse(document.getElementById('overview').innerText)[0].count;
	document.getElementById('doing').innerText=overview.doing;
	document.getElementById('finished').innerText=overview.total-overview.doing-overview.repeal;
	document.getElementById('timeout').innerText=overview.timeout;
}
</script>
</body>
</html>