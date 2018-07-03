<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>太仓海警支队--用户管理</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.2.css"/>
<style type="text/css">
.dialog-content{ background-color:#eee;}
.dataFill{ background-color:#fff; border-bottom:1px solid #ccc; padding-bottom:10px; box-shadow:0 2px 5px rgba(0,0,0,.1);}
.buttonBar{ padding:10px;}
.lay2col{ max-width:600;}
.lay2col>*{ min-width:270px;}
.buttonBar .tipMessage{ padding-top:0;}
.tipMessage .fa{ padding:6px 20px; background-color:#fff; border:1px solid #ccc;}
.tipMessage .warning{ border-color:#f90;}
.tipMessage .alert{ border-color:red;}
.tipMessage .ok{ border-color:green;}
.tipMessage .loading{ border-color:#697ac1;}
td .fa-user:before{ font-size:1.2em; color:#4569DD; margin-right:3px; vertical-align:middle;}
</style>
<body>
<div id="app" class="outFrame rexFrame nb nl header">
	<div class="rexTopbar">
		<h2 class="bkStyle2"><span class="fa fa-group"></span> 用户管理</h2>
		<span class="tools">
			<span class="count">当前共 <strong>{{list.length}}</strong> 个用户</span>
			<button class="rexButton ss fa fa-plus warning" @click="showDialog('userop')" title="添加新用户"></button>
		</span>
	</div>
	<div class="rexRightpart">
		<div class="tableFrame">
			<table class="rexTable">
				<tr>
					<th width="40"></th>
					<th>类型</th>
					<th>用户名</th>
					<th>部门</th>
					<th>职务</th>
					<th>姓名</th>
					<th>电话</th>
					<th width="90">操作</th>
				</tr>
				<tr v-for="(item,index) in list">
					<td>{{index+1}}</td>
					<td>{{item.tname}}</td>
					<td><span class="fa fa-user" v-if="me.username==item.username"></span>{{item.username}}</td>
					<td>{{item.pname || '--'}}</td>
					<td>{{item.jname || '--'}}</td>
					<td>{{item.realname}}</td>
					<td>{{item.telnumber}}</td>
					<td>
						<button class="rexButton ss alert fa fa-trash-o" v-if="item.tid!=cfg.UM" @click="showDialog('opsure',index)" title="删除该用户"></button>
						<button class="rexButton ss infor fa fa-pencil" @click="showDialog('userop',index)" title="编辑用户资料"></button>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div id="userop" class="extDialog" noclick="noclick">
		<div class="dialogFrame">
			<div class="dialog-title">
				<span class="opBnt right fa fa-lg fa-times" v-if="load==false" @click="hideDialog('userop')"></span>
				<h4 class="t3"><span class="fa fa-plus" v-if="op=='add'"> 添加新用户</span><span class="fa fa-pencil" v-if="op=='edit'"> 编辑用户信息</span></h4>
			</div>
			<div class="dialog-content">
				<div class="dataFill">
					<ul class="lay2col">
						<li class="formpart">
							<label class="rexLabel">账户类型</label><span class="request" v-if="op=='add'">
								<select class="rexSelect" v-bind:class="{'warning':chk[0].obj=='usertype'}" v-model="edit.tid" v-bind:disabled="load">
									<option value=""></option>
									<option v-for="item in usertype" v-bind:value="item.tid">{{item.tname}}</option>
								</select>
							</span><span v-if="op=='edit'">{{edit.tname}}</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">用&ensp;户&ensp;名</label><span class="request" v-if="op=='add'">
								<input class="rexInput" type="text" v-bind:class="{'warning':chk[0].obj=='username'}" v-model.trim="edit.username" v-bind:disabled="load" maxlength="30" autocomplete="off"/>
							</span><span v-if="op=='edit'">{{edit.username}}</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">部&emsp;&emsp;门</label><span v-bind:class="{'request':filterDPMT.length}" v-if="op=='add'">
								<select class="rexSelect" v-bind:class="{'warning':chk[0].obj=='department'}" v-bind:disabled="filterDPMT.length==0 || load" v-model="edit.pid">
									<option value=""></option>
									<option v-for="item in filterDPMT" v-bind:value="item.pid">{{item.pname}}</option>
								</select>
							</span><span v-if="op=='edit'">{{edit.pname || '--'}}</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">职&emsp;&emsp;务</label><span v-bind:class="{'request':filterJOB.length}" v-if="op=='add'">
								<select class="rexSelect" v-bind:class="{'warning':chk[0].obj=='job'}" v-bind:disabled="filterJOB.length==0 || load" v-model="edit.jid">
									<option value=""></option>
									<option v-for="item in filterJOB" v-bind:value="item.jid">{{item.jname}}</option>
								</select>
							</span><span v-if="op=='edit'">{{edit.jname || '--'}}</span>
						</li>
						<li class="formpart" v-if="op=='edit' && isManager==false">
							<label class="rexLabel">原始密码</label><span title="如不需要修改密码，则不需要填写该项">
								<input class="rexInput" type="password" v-bind:class="{'warning':chk[0].obj=='oldpassword'}" v-model.trim="edit.oldpassword" v-bind:disabled="load" maxlength="30"/>
							</span>
						</li>
						<li class="formpart" v-if="op=='edit' && isManager==false">
						</li>
						<li class="formpart">
							<label class="rexLabel">新&ensp;密&ensp;码</label><span v-bind:class="{'request':(op=='add' || edit.oldpassword)}">
								<input class="rexInput" type="password" v-bind:class="{'warning':chk[0].obj=='newpassword' || chk[0].obj=='all'}" v-model.trim="edit.newpassword" v-bind:disabled="load" maxlength="30"/>
							</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">密码确认</label><span v-bind:class="{'request':(op=='add' || edit.oldpassword)}">
								<input class="rexInput" type="password" v-bind:class="{'warning':chk[0].obj=='repassword' || chk[0].obj=='all'}" v-model.trim="edit.repassword" v-bind:disabled="load" maxlength="30"/>
							</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">姓&emsp;&emsp;名</label><span>
								<input class="rexInput" type="text" v-bind:class="{'warning':chk[0].obj=='realname'}" v-model.trim="edit.realname" v-bind:disabled="load" maxlength="20" autocomplete="off"/>
							</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">联系电话</label><span>
								<input class="rexInput" type="text" v-bind:class="{'warning':chk[0].obj=='telnumber'}" v-model.trim="edit.telnumber" v-bind:disabled="load" maxlength="15" autocomplete="off"/>
							</span>
						</li>
					</ul>
				</div>
				<div class="buttonBar">
					<div class="tipMessage" v-if="chk[0].flag">
						<span class="fa" v-bind:class="chk[0].flag"> {{chk[0].msg}}</span>
					</div>
					<button class="rexButton infor" @click="sendData()">提&emsp;交</button>
				</div>
			</div>
		</div>
	</div>
	
	<div id="opsure" class="extDialog">
		<div class="dialogFrame">
			<div class="dialog-title warning">
				<h4 class="t3"><span class="fa fa-question"></span> <span class="diy">确认操作</span></h4>
			</div>
			<div class="dialog-buttonBar">
				<div class="bntInside">
					<button class="rexButton opBnt" @click="hideDialog('opsure')" v-bind:disabled="load">取 消</button>
					<button class="rexButton opBnt alert" @click="sendDelData()" v-bind:disabled="load">确 定</button>
				</div>
			</div>
			<div class="dialog-content">
				<div class="sP1 diy"></div>
				<div class="tipMessage" v-if="chk[0].flag">
					<span class="fa" v-bind:class="chk[0].flag"> {{chk[0].msg}}</span>
				</div>
			</div>
		</div>
  </div>
</div>

<div id="user" class="dataField"><?php echo json_encode($user); ?></div>
<div id="account" class="dataField"><?php echo json_encode($account); ?></div>
<div id="department" class="dataField"><?php echo json_encode($department); ?></div>
<div id="job" class="dataField"><?php echo json_encode($job); ?></div>
<div id="usertype" class="dataField"><?php echo json_encode($usertype); ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/dialog1.3.1.js"></script>
	<script src="/script/md5.min.js"></script>
	<script src="/script/config.js"></script>
	<script src="/script/userlist.js"></script>
</body>
</html>