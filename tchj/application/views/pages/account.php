<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>账户管理</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
<style type="text/css">
.outFrame{ background-color:#eee;}
.pageHeader{ margin:0; border:5px solid #fff;}
.fillForm{ width:100%; background-color:#fff; padding-bottom:10px;}
.fillForm .itempart{ width:50%; min-width:340px; max-width:600px; margin:0 auto; padding:5px;}
.fillForm .rexInput,.fillForm .rexTxtarea{ width:200px;}
.btnBar{ text-align:center; padding-top:10px; border-top:1px solid #ccc; box-shadow:0 2px 3px rgba(0,0,0,.3) inset;}
.btnBar .rexButton{ width:100px;}
</style>
</head>
<body>
<div id="app" class="outFrame">
	<div class="pageHeader">
		<h2 class="fa fa-user"> {{pageTitle}}</h2>
	</div>
	<div class="fillForm">
		<div class="itempart">
			<label class="rexLabel">账户类型</label><span v-bind:class="{'request':op=='add'}">
				<select class="rexSelect" v-if="op=='add'" v-bind:class="{'warning':chk.obj=='usertype'}" v-model="edit.usertype" v-bind:disabled="load">
					<option value=""></option>
					<option v-for="item in usertype" v-bind:value="item.tid">{{item.tname}}</option>
				</select>
				<span v-if="op=='edit'">{{edit.usertype}}</span>
			</span>
		</div>
		<div class="itempart">
			<label class="rexLabel">用&ensp;户&ensp;名</label><span v-bind:class="{'request':op=='add'}">
				<input class="rexInput" type="text" v-if="op=='add'" v-bind:class="{'warning':chk.obj=='username'}" v-model.trim="edit.username" v-bind:disabled="load" maxlength="30" autocomplete="off"/>
				<span v-if="op=='edit'">{{edit.username}}</span>
			</span>
		</div>
		<div class="itempart" v-if="op=='edit' && isManager==false">
			<label class="rexLabel">原始密码</label><span title="如不需要修改密码，则不需要填写该项">
				<input class="rexInput" type="password" v-bind:class="{'warning':chk.obj=='oldpwd'}" v-model.trim="edit.oldpwd" v-bind:disabled="load" maxlength="30"/>
			</span>
		</div>
		<div class="itempart">
			<label class="rexLabel">新&ensp;密&ensp;码</label><span v-bind:class="{'request':(op=='add' || edit.oldpwd)}">
				<input class="rexInput" type="password" v-bind:class="{'warning':chk.obj=='newpwd' || chk.obj=='allpwd'}" v-model.trim="edit.newpwd" v-bind:disabled="load" maxlength="30"/>
			</span>
		</div>
		<div class="itempart">
			<label class="rexLabel">密码确认</label><span v-bind:class="{'request':(op=='add' || edit.oldpwd)}">
				<input class="rexInput" type="password" v-bind:class="{'warning':chk.obj=='repwd' || chk.obj=='allpwd'}" v-model.trim="edit.repwd" v-bind:disabled="load" maxlength="30"/>
			</span>
		</div>
		<div class="itempart">
			<label class="rexLabel">部&emsp;&emsp;门</label><span v-bind:class="{'request':filterDPMT.length}">
				<select class="rexSelect" v-if="op=='add'" v-bind:class="{'warning':chk.obj=='department'}" v-bind:disabled="filterDPMT.length==0 || load" v-model="edit.department">
					<option value=""></option>
					<option v-for="item in filterDPMT" v-bind:value="item.pid">{{item.pname}}</option>
				</select>
				<span v-if="op=='edit'">{{edit.department}}</span>
			</span>
		</div>
		<div class="itempart">
			<label class="rexLabel">职&emsp;&emsp;务</label><span v-bind:class="{'request':filterJOB.length}">
				<select class="rexSelect" v-if="op=='add'" v-bind:class="{'warning':chk.obj=='job'}" v-bind:disabled="filterJOB.length==0 || load" v-model="edit.job">
					<option value=""></option>
					<option v-for="item in filterJOB" v-bind:value="item.jid">{{item.jname}}</option>
				</select>
				<span v-if="op=='edit'">{{edit.job}}</span>
			</span>
		</div>
		<div class="itempart">
			<label class="rexLabel">姓&emsp;&emsp;名</label><span>
				<input class="rexInput" type="text" v-bind:class="{'warning':chk.obj=='name'}" v-model.trim="edit.name" v-bind:disabled="load" maxlength="20" autocomplete="off"/>
			</span>
		</div>
		<div class="itempart">
			<label class="rexLabel">联系电话</label><span>
				<input class="rexInput" type="text" v-bind:class="{'warning':chk.obj=='tel'}" v-model.trim="edit.tel" v-bind:disabled="load" maxlength="15" autocomplete="off"/>
			</span>
		</div>
		<div class="tipMessage" v-bind:class="chk.flag" v-if="chk.flag">
			<span class="fa fa-lg" v-bind:class="{'fa-warning':chk.flag=='warning','fa-times-circle':chk.flag=='alert','fa-check-circle':chk.flag=='ok'}"> {{chk.msg}}</span>
		</div>
	</div>
	<div class="btnBar">
		<button class="rexButton" v-if="op=='edit'" @click="resetData" v-bind:disabled="load">还原</button>
		<button class="rexButton infor" @click="sendData" v-bind:disabled="load">提交</button>
	</div>
</div>
<div id="URL" class="dataField"><?php echo $config['URLSTR']; ?></div>
<div id="op" class="dataField"><?php echo $op; ?></div>
<div id="account" class="dataField"><?php echo json_encode($account); ?></div>
<div id="USERM" class="dataField"><?php echo $config['USERM']; ?></div>
<div id="USERD" class="dataField"><?php echo $config['USERD']; ?></div>
<div id="USERU" class="dataField"><?php echo $config['USERU']; ?></div>
<div id="department" class="dataField"><?php echo json_encode($department); ?></div>
<div id="job" class="dataField"><?php echo json_encode($job); ?></div>
<div id="usertype" class="dataField"><?php echo json_encode($usertype); ?></div>
<div id="user" class="dataField"><?php if ($user==''){ echo '';}else{echo json_encode($user);} ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/md5.min.js"></script>
	<script src="/script/config.js"></script>
<script>
var vu=new Vue({
	el: "#app",
	data:{
		op: $("#op").text(),  //操作类型
		pageTitle: '',
		USERM: $("#USERM").text(),
		USERD: $("#USERD").text(),
		USERU: $("#USERU").text(),
		me: JSON.parse($("#account").text()),
		usertype: '',
		department: '',
		job: '',
		user: '',
		isManager: false,
		edit:{
			uid:'',
			usertype:'',
			username:'',
			oldpwd:'',
			newpwd:'',
			repwd:'',
			department:'',
			job:'',
			name:'',
			tel:''
		},
		chk:{
			flag:'',
			msg:'',
			obj:''
		},
		load: false           //数据发送标志
	},
	computed:{
		//准备部门下拉框内容
		filterDPMT: function(){
			if (this.op=='edit') return [];
			var tempArray=[];
			if (this.edit.usertype==this.USERD || this.edit.usertype==this.USERU){
				for (var x in this.department){
					if (this.department[x].plevel==this.usertype[this.edit.usertype].plevel){
						tempArray.push(this.department[x]);
					}
				}
			}
			this.edit.department="";
			return tempArray;
		},
		//职务下拉框内容
		filterJOB: function(){
			if (this.op=='edit') return [];
			var tempArray=[];
			if (this.edit.usertype!=''){
				if (this.usertype[this.edit.usertype].job_flag=="1"){
					for (var x in this.job){
						tempArray.push(this.job[x]);
					}
				}
			}
			this.edit.job="";
			return tempArray;
		}
	},
	methods:{
		//初始化edit的用户数据
		editInit: function(){
			this.edit.uid=this.user['uid'];
			this.edit.usertype=this.usertype.tname;
			this.edit.username=this.user.username;
			this.edit.department=this.department==''? '--': this.department.pname;
			this.edit.job=this.job==''? '--': this.job.jname;
			this.edit.name=this.user['realname']? this.user['realname']:'';
			this.edit.tel=this.user['telnumber']? this.user['telnumber']:'';
		},
		//编辑数据还原
		resetData: function(){
			if (this.op=='add'){
				for (var x in this.edit){
					this.edit[x]='';
				}
			}else{
				this.edit.oldpwd='';
				this.edit.newpwd='';
				this.edit.repwd='';
				this.edit.name=this.user['realname']? this.user['realname']:'';
				this.edit.tel=this.user['telnumber']? this.user['telnumber']:'';
			}
		},
		//检查数据完整性
		checkData: function(){
			var msg='',obj='';
			for (var i=0; i<1; i++){
				if (this.op=='add'){   //添加用户规范检测
					if (this.edit.usertype==''){ //账户类型
						msg='请选择新加用户的账户类型';
						obj='usertype';
						break;
					}
					if (REG.uname.test(this.edit.username)===false){ //用户名
						msg='用户名应该是3~30个英文或者数字组成的字符串';
						obj='username';
						break;
					}
					if (REG.upwd.test(this.edit.newpwd)===false){ //密码规范
						msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
						obj='newpwd';
						break;
					}
					if (REG.upwd.test(this.edit.repwd)===false){  //重复密码
						msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
						obj='repwd';
						break;
					}
					if (this.edit.newpwd!==this.edit.repwd){ //密码逻辑
						msg='输入的密码与确认密码不匹配';
						obj='allpwd';
						break;
					}
					if (this.filterDPMT.length>0 && this.edit.department===''){ //部门选择
						msg='请选择新加用户所在的部门';
						obj='department';
						break;
					}
					if (this.filterJOB.length>0 && this.edit.job===''){ //职务选择
						msg='请选择新加用户的职务';
						obj='job';
						break;
					}
				}else{   //修改用户规范检测
					if (this.isManager==false){
						if (this.edit.oldpwd!=''){
							if (this.edit.newpwd==''){
								msg='如果要修改密码，请填写新密码和密码确认，否则请不要填写原始密码';
								obj='newpwd';
								break;
							}
							if (this.edit.repwd==''){
								msg='如果要修改密码，请填写新密码和密码确认，否则请不要填写原始密码';
								obj='repwd';
								break;
							}
							//密码规范
							if (REG.upwd.test(this.edit.oldpwd)===false){
								msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
								obj='oldpwd';
								break;
							}
							if (REG.upwd.test(this.edit.newpwd)===false){
								msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
								obj='newpwd';
								break;
							}
							if (REG.upwd.test(this.edit.repwd)===false){
								msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
								obj='repwd';
								break;
							}
						}else{
							if (this.edit.newpwd!='' || this.edit.repwd!=''){
								msg='如果要修改密码，请输入原始密码';
								obj='oldpwd';
								break;
							}
						}
					}else{
						//密码规范
						if (this.edit.newpwd!='' && REG.upwd.test(this.edit.newpwd)===false){
							msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
							obj='newpwd';
							break;
						}
						if (this.edit.repwd!='' && REG.upwd.test(this.edit.repwd)===false){
							msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
							obj='repwd';
							break;
						}
					}
					//密码逻辑
					if (this.edit.newpwd!==this.edit.repwd){
						msg='输入的密码与确认密码不匹配';
						obj='allpwd';
						break;
					}
				}
				//姓名填写
				if (this.edit.name!=='' && REG.name.check(this.edit.name)===false){
					msg='姓名填写有误';
					obj='name';
					break;
				}
				//电话填写
				if (this.edit.tel!=='' && REG.tel.test(this.edit.tel)===false){
					msg='联系电话填写有误';
					obj='tel';
					break;
				}
				//如果是修改，判定资料是否变更，如没有变更则无需提交
				if (this.op=='edit'){
					if (this.edit.newpwd=='' && this.edit.name==this.user.realname && this.edit.tel==this.user.telnumber){
						msg='没有对用户信息做任何修改，无需提交';
						break;
					}
				}
			}
			if (msg!=''){
				this.setChk('warning',msg,obj);
				return false;
			}
			return true;
		},
		//提交数据
		sendData: function(){
			if (this.checkData()){
				var temp;
				var nowdata={};
				if (this.op=='add'){
					temp=['usertype','username','newpwd','department','job','name','tel'];
					for (var i=0; i<temp.length; i++){
						nowdata[temp[i]]=this.edit[temp[i]];
					}
					nowdata.newpwd=md5(nowdata.newpwd);
				}else{
					temp=['uid','oldpwd','newpwd','name','tel'];
					for (var i=0; i<temp.length; i++){
						nowdata[temp[i]]=this.edit[temp[i]];
					}
					if (nowdata.oldpwd!='') nowdata.oldpwd=md5(nowdata.oldpwd);
					if (nowdata.newpwd!='') nowdata.newpwd=md5(nowdata.newpwd);
				}
				ajax.data=nowdata;
				ajax.send();
			}
		},
		//设置提示信息
		setChk: function(classStr,msg,obj){
			this.chk.flag=classStr;
			this.chk.msg=msg;
			if (obj){
				this.chk.obj=obj;
			}else{
				this.chk.obj='';
			}
		},
		//清除错误信息
		clearChk: function(){
			for (var x in this.chk){
				this.chk[x]='';
			}
		}
	},
	created:function(){
		this.isManager=this.me.tid==this.USERM? true:false;
		var temp={
			'department':'pid',
			'job':'jid',
			'usertype':'tid'
		};
		if (this.op=='add'){
			for (var x in temp){
				this[x]=JSON.array2Object(JSON.parse($("#"+x).text()),temp[x]);
			}
		}else{
			for (var x in temp){
				temp[x]=JSON.parse($("#"+x).text());
				if (temp[x]!='') this[x]=temp[x][0];
			}
			this.user=JSON.parse($("#user").text());
		}

		if (this.op=='add'){
			this.pageTitle='添加新用户资料';
		}else{
			this.pageTitle='修改用户资料';
			this.editInit();
		}
	},
	watch:{
		'edit.usertype':function(newVal){
			if (this.chk.obj=='usertype') this.clearChk();
		},
		'edit.username':function(newVal){
			if (this.chk.obj=='username') this.clearChk();
		},
		'edit.oldpwd':function(newVal){
			if (this.chk.obj=='oldpwd') this.clearChk();
		},
		'edit.newpwd':function(newVal){
			if (this.chk.obj=='newpwd' || this.chk.obj=='allpwd') this.clearChk();
		},
		'edit.repwd':function(newVal){
			if (this.chk.obj=='repwd' || this.chk.obj=='allpwd') this.clearChk();
		},
		'edit.department':function(newVal){
			if (this.chk.obj=='department') this.clearChk();
		},
		'edit.job':function(newVal){
			if (this.chk.obj=='job') this.clearChk();
		},
		'edit.name':function(newVal){
			if (this.chk.obj=='name') this.clearChk();
		},
		'edit.tel':function(newVal){
			if (this.chk.obj=='tel') this.clearChk();
		}
	}
});

var ajax=new relaxAJAX();
if (vu.op=='add'){
	ajax.url=CFGURL + 'pages/accountop/add';
}else{
	ajax.url=CFGURL + 'pages/accountop/edit';
}
ajax.before=function(){
	vu.load=true;
	vu.setChk('loading','正在提交数据，请稍等...');
};
ajax.error=function(code,msg){
	vu.load=false;
	vu.setChk('alert',code+' '+msg);
};
ajax.success=function(data){
	if (data.code){
		vu.load=false;
		vu.setChk('alert',data.message);
	}else{
		var msg;
		if (vu.op=='add'){
			msg='用户 '+vu.edit.username+' 已经添加成功！';
		}else{
			msg='用户 '+vu.user.username+' 资料已经修改成功！';
			vu.user.realname=vu.edit.name;
			vu.user.telnumber=vu.edit.tel;
		}
		vu.setChk('ok',msg);
		setTimeout(function(){
			vu.resetData();
			vu.clearChk();
			vu.load=false;
		},2000);
	}
};
</script>
</body>
</html>