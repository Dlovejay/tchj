<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>太仓海警支队--部门管理</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.2.css"/>
<style type="text/css">
.formpart .rexButton{ margin:3px;}
.lay2col{ width:80%; max-width:360px; min-width:240px;}
.extMenu.right .dialogFrame{ width:50%; min-width:500px;}
</style>
<body>
<div id="app" class="outFrame rexFrame nb nl header">
	<div class="rexTopbar">
		<h2 class="bkStyle1 tCT"><span class="fa fa-sitemap"></span> 部门管理</h2>
	</div>
	<div class="rexRightpart">
		<div class="formpart" v-for="(item,key) in level">
			<label class="rexLabel">{{levelName[key]}}</label><span>
				<button class="rexButton" v-for="dpt in item" @click="showDialog('edit',dpt.pid)">{{dpt.pname}}</button><button class="rexButton fa fa-plus infor" @click="showDialog('add',key)" v-if="key!=0"></button>
			</span>
		</div>
	</div>
	
	<div id="departmentop" class="extMenu right" noclick="noclick">
		<div class="dialogFrame">
			<div class="dialog-title">
				<span class="opBnt right fa fa-lg fa-times" v-if="load==false" @click="hideDialog('departmentop')"></span>
				<h4 class="t3"><span class="fa fa-pencil" v-if="op=='edit'"> 操作部门信息</span><span class="fa fa-plus" v-if="op=='add'"> 添加部门信息</span></h4>
			</div>
			<div class="dialog-buttonBar">
				<div class="bntInside">
					<button class="rexButton opBnt alert" v-bind:disabled="load" v-if="edit.plevel>0 && op!='add'" @click="showDialog('opsure')">删除部门</button>
					<button class="rexButton opBnt infor" v-bind:disabled="load" @click="sendData()">提交信息</button>
				</div>
			</div>
			<div class="dialog-content">
				<div class="lay2col">
					<div class="formpart alone" v-if="op!='add'">
						<label class="rexLabel">当前部门</label><span>{{edit.pname}}</span>
					</div>
					<div class="formpart alone">
						<label class="rexLabel">部门类型</label><span>{{getTypeName}}</span>
					</div>
					<div class="formpart alone">
						<label class="rexLabel" v-if="op!='add'">修改名称</label><label class="rexLabel" v-if="op=='add'">部门名称</label><span class="request">
							<input type="text" class="rexInput" v-bind:disabled="load" v-bind:class="{'warning':chk[0].obj=='pname'}" v-model="edit.pname2"/>
						</span>
					</div>
				</div>
				<div class="tipMessage">
					<span class="fa" v-bind:class="chk[0].flag"> {{chk[0].msg}}</span>
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
					<button class="rexButton opBnt alert" @click="sendData()" v-bind:disabled="load">确 定</button>
				</div>
			</div>
			<div class="dialog-content">
				<div class="sP1 diy"></div>
			</div>
		</div>
  </div>

</div>

<div id="department" class="dataField"><?php echo json_encode($department); ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/dialog1.3.1.js"></script>
	<script src="/script/config.js"></script>
	<script src="/script/department.js"></script>
</body>
</html>