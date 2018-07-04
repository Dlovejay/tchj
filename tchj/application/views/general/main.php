<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>太仓海警支队--管理系统主页</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.2.css"/>
	<link rel="stylesheet" href="/style/main.css"/>
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
			<div class="userMenu"><button class="rexButton sBlk" @click="showDialog('accountedit')">我的账户</button></div>
			<div class="userMenu"><a class="rexButton sBlk" v-bind:href="url.loginout">退出登录</a></div>
		</div>
  </div>
  <nav class="rexLeftpart t3">
		<a class="fa fa-sitemap" v-if="me.tid==cfg.UM" v-bind:href="url.department" target="contentshow" v-bind:class="{'sel':nowSel==1}" @click="setSel(1)">&ensp;部门管理</a>
		<a class="fa fa-list-alt" v-if="me.tid==cfg.UM" v-bind:href="url.job" target="contentshow" v-bind:class="{'sel':nowSel==2}" @click="setSel(2)">&ensp;职务管理</a>
		<a class="fa fa-group" v-if="me.tid==cfg.UM" v-bind:href="url.user" target="contentshow" v-bind:class="{'sel':nowSel==3}" @click="setSel(3)">&ensp;用户管理</a>
		<a class="splitBar fa fa-bar-chart-o" v-bind:href="url.overview" target="contentshow" v-bind:class="{'sel':nowSel==4}" @click="setSel(4)">&ensp;工作概览</a>
		<a class="fa fa-calendar" v-bind:href="url.task" target="contentshow" v-bind:class="{'sel':nowSel==5}" @click="setSel(5)">&ensp;任务列表</a>
		<a class="fa fa-paste" v-bind:href="url.consult" target="contentshow" v-bind:class="{'sel':nowSel==6}" @click="setSel(6)">&ensp;请示列表</a>
  </nav>
	<div class="rexRightpart">
		<iframe v-bind:src="url.overview" name="contentshow"></iframe>
	</div>
	
	<div id="accountedit" class="extDialog" noclick="noclick">
		<div class="dialogFrame">
			<div class="dialog-title">
				<span class="opBnt right fa fa-lg fa-times" v-if="load==false" @click="hideDialog('accountedit')"></span>
				<h4 class="t3"><span class="fa fa-pencil"></span>&emsp;<span class="diy">编辑当前用户</span></h4>
			</div>
			<div class="dialog-content">
				<div class="dataFill">
					<ul class="lay2col">
						<li class="formpart view">
							<label class="rexLabel">用户类型</label><span>{{me.tname}}</span>
						</li>
						<li class="formpart view">
							<label class="rexLabel">用&ensp;户&ensp;名</label><span>{{me.username}}</span>
						</li>
						<li class="formpart view">
							<label class="rexLabel">部&emsp;&emsp;门</label><span>{{me.pname? me.pname:'--'}}</span>
						</li>
						<li class="formpart view">
							<label class="rexLabel">职&emsp;&emsp;务</label><span>{{me.jname? me.jname:'--'}}</span>
						</li>
						<li class="formpart" v-if="me.tid!=cfg.UM">
							<label class="rexLabel">原始密码</label><span>
								<input type="password" class="rexInput" v-model="edit.oldpassword" v-bind:class="{'warning':chk[0].obj=='oldpassword'}" title="若需修改密码，请填写原始密码" max="30"/>
							</span>
						</li>
						<li class="formpart" v-if="me.tid!=cfg.UM"></li>
						<li class="formpart">
							<label class="rexLabel">新&ensp;密&ensp;码</label><span v-bind:class="{'request':edit.oldpassword}">
								<input type="password" class="rexInput" v-model="edit.newpassword" v-bind:class="{'warning':chk[0].obj=='newpassword' || chk[0].obj=='all'}" max="30"/>
							</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">密码确认</label><span v-bind:class="{'request':edit.oldpassword}">
								<input type="password" class="rexInput" v-model="edit.repassword" v-bind:class="{'warning':chk[0].obj=='repassword' || chk[0].obj=='all'}" max="30"/>
							</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">姓&emsp;&emsp;名</label><span>
								<input type="text" class="rexInput"  v-model="edit.realname" v-bind:class="{'warning':chk[0].obj=='realname'}" max="30"/>
							</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">联系电话</label><span>
								<input type="text" class="rexInput"  v-model="edit.telnumber" v-bind:class="{'warning':chk[0].obj=='telnumber'}" max="30"/>
							</span>
						</li>
					</ul>
				</div>
				<div class="buttonBar">
					<div class="tipMessage" v-if="chk[0].flag">
						<span class="fa" v-bind:class="chk[0].flag"> {{chk[0].msg}}</span>
					</div>
					<button class="rexButton infor" @click="sendEdit()">提交修改</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="account" class="dataField"><?php echo json_encode($account); ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/dialog1.3.1.js"></script>
	<script src="/script/md5.min.js"></script>
	<script src="/script/config.js"></script>
	<script src="/script/main.js"></script>
</body>
</html>