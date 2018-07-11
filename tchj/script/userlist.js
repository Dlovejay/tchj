var vu=new Vue({
	el: "#app",
	data:{
		cfg: CFG,
		me: JSON.parse($("#account").text()),
		usertype: '',
		department: '',
		job: '',
		list: '',   //数据的数组列表
		relist: {}, //数据的主键参照对象
		isManager: false,
		edit:{
			uid:'',
			tid:'',
			tname:'',
			username:'',
			oldpassword:'',
			newpassword:'',
			repassword:'',
			pid:'',
			pname:'',
			jid:'',
			jname:'',
			realname:'',
			telnumber:''
		},
		chk:'',
		op:'',
		load: false           //数据发送标志
	},
	computed:{
		//准备部门下拉框内容
		filterDPMT: function(){
			if (this.op=='edit') return [];
			var tempArray=[];
			if (this.edit.tid==this.cfg.UL || this.edit.tid==this.cfg.UD || this.edit.tid==this.cfg.UU){
				for (var x in this.department){
					if (this.department[x].plevel==this.usertype[this.edit.tid].plevel){
						tempArray.push(this.department[x]);
					}
				}
			}
			this.edit.pid="";
			return tempArray;
		},
		//职务下拉框内容
		filterJOB: function(){
			if (this.op=='edit') return [];
			var tempArray=[];
			if (this.edit.tid!=''){
				if (this.usertype[this.edit.tid].job_flag=="1"){
					for (var x in this.job){
						tempArray.push(this.job[x]);
					}
				}
			}
			this.edit.jid="";
			return tempArray;
		}
	},
	methods:{
		_getChkobj: function(num){  //获得检查对象
			var tempArray=[];
			for (var i=0; i<num; i++){
				tempArray.push({
					flag:'',
					msg:'',
					obj:''
				});
			}
			return tempArray;
		},
		setChk: function(index,flag,msg,obj){ //设置提示信息
			if (!this.chk[index]) return;
			var temp=this.chk[index];
			temp.flag=flag;
			temp.msg=msg;
			if (obj){
				temp.obj=obj;
			}else{
				temp.obj='';
			}
		},
		clearChk: function(index){ //清除提示信息
			if (index===undefined){
				for (var i=0; i<this.chk.length; i++){
					var temp=this.chk[i];
					for (var x in temp){
						temp[x]='';
					}
				}
			}else if(!this.chk[index]){
				return;
			}else{
				for (var x in this.chk[index]){
					this.chk[index][x]='';
				}
			}
		},
		showDialog:function(name,index){
			if (name=='userop'){
				if (index!==undefined && this.list[index]){
					this.op='edit';
					this.editInit(this.list[index]);
				}else{
					this.op='add';
				}
				dialog.open(name);
			}else{
				this.op='del';
				this.edit.uid=this.list[index].uid;
				dialog.open(name,{content:'请确认是否删除用户 <strong>'+ this.list[index].username+'</strong> ?'});
			}
		},
		hideDialog:function(name){
			if (name=='userop'){
				this.op='';
				for (var x in this.edit){
					this.edit[x]='';
				}
			}else{
				this.op='';
				this.edit.uid='';
			}
			this.clearChk(0);
			dialog.close(name);
		},
		//初始化edit的用户数据
		editInit: function(temp){
			this.edit.uid=temp.uid;
			this.edit.tname=temp.tname;
			this.edit.username=temp.username;
			this.edit.pname=temp.pname;
			this.edit.jname=temp.jname;
			this.edit.realname=temp.realname;
			this.edit.telnumber=temp.telnumber;
			this.edit.oldpassword='';
			this.edit.newpassword='';
			this.edit.repassword='';
		},
		//检查数据完整性
		checkData: function(){
			var msg='',obj='';
			for (var i=0; i<1; i++){
				if (this.op=='add'){   //添加用户规范检测
					if (this.edit.tid==''){
						msg='请选择新加用户的账户类型';
						obj='usertype';
						break;
					}
					if (REG.uname.test(this.edit.username)===false){ //用户名
						msg='用户名应该是3~30个英文或者数字组成的字符串';
						obj='username';
						break;
					}
					if (REG.upwd.test(this.edit.newpassword)===false){ //密码规范
						msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
						obj='newpassword';
						break;
					}
					if (this.edit.newpassword!==this.edit.repassword){ //密码逻辑
						msg='输入的密码与确认密码不匹配';
						obj='all';
						break;
					}
					if (this.filterDPMT.length>0 && this.edit.pid===''){ //部门选择
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
						if (this.edit.oldpassword!=''){
							if (this.edit.newpassword==''){
								msg='如果要修改密码，请填写新密码和密码确认，否则请不要填写原始密码';
								obj='newpassword';
								break;
							}
							if (this.edit.repassword==''){
								msg='如果要修改密码，请填写新密码和密码确认，否则请不要填写原始密码';
								obj='repassword';
								break;
							}
							//密码规范
							if (REG.upwd.test(this.edit.oldpassword)===false){
								msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
								obj='oldpassword';
								break;
							}
							if (REG.upwd.test(this.edit.newpassword)===false){
								msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
								obj='newpassword';
								break;
							}
						}else{
							if (this.edit.newpassword!='' || this.edit.repassword!=''){
								msg='如果要修改密码，请输入原始密码';
								obj='oldpassword';
								break;
							}
						}
					}else{
						//密码规范
						if (this.edit.newpassword!='' && REG.upwd.test(this.edit.newpassword)===false){
							msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
							obj='newpassword';
							break;
						}
					}
					//密码逻辑
					if (this.edit.newpassword!==this.edit.repassword){
						msg='输入的密码与确认密码不匹配';
						obj='all';
						break;
					}
				}
				//姓名填写
				if (this.edit.realname!=='' && REG.name.check(this.edit.realname)===false){
					msg='姓名填写有误';
					obj='realname';
					break;
				}
				//电话填写
				if (this.edit.telnumber!=='' && REG.tel.test(this.edit.telnumber)===false){
					msg='联系电话填写有误';
					obj='telnumber';
					break;
				}
				//如果是修改，判定资料是否变更，如没有变更则无需提交
				if (this.op=='edit'){
					if (this.edit.newpassword=='' && this.edit.realname==this.list[this.relist[this.edit.uid]].realname && this.edit.telnumber==this.list[this.relist[this.edit.uid]].telnumber){
						msg='没有对用户信息做任何修改，无需提交';
						break;
					}
				}
			}
			if (msg!=''){
				this.setChk(0,'warning',msg,obj);
				return false;
			}
			return true;
		},
		sendData: function(){   //提交数据
			if (this.checkData()){
				if (this.op=='add'){
					ajax.data={
						tid: this.edit.tid,
						username: this.edit.username,
						password: md5(this.edit.newpassword),
						pid: this.edit.pid,
						jid: this.edit.jid,
						realname: this.edit.realname,
						telnumber: this.edit.telnumber
					};
					ajax.url=URL.useradd;
				}else{
					ajax.data={
						uid: this.edit.uid,
						newpassword: this.edit.newpassword? md5(this.edit.newpassword):'',
						realname: this.edit.realname,
						telnumber: this.edit.telnumber
					}
					ajax.url=URL.useredit;
				}
				ajax.send();
			}
		},
		sendDelData: function(){  //提交删除数据
			ajax.data={
				uid: this.edit.uid
			}
			ajax.url=URL.userdrop;
			ajax.send();
		}
	},
	created:function(){
		this.isManager=this.me.tid==this.cfg.UM? true:false;
		this.chk=this._getChkobj(1);
		this.list=JSON.parse($('#user').text());
		for (var i=0; i<this.list.length; i++){
			this.relist[this.list[i].uid]=i;
			if (!this.list[i].pname) this.list[i].pname='';
			if (!this.list[i].jname) this.list[i].jname='';
		}
		this.department=JSON.array2Object(JSON.parse($('#department').text()),'pid');
		this.job=JSON.array2Object(JSON.parse($('#job').text()),'jid');
		this.usertype=JSON.array2Object(JSON.parse($('#usertype').text()),'tid');
	},
	watch:{
		'edit.tid':function(newVal){
			if (this.chk[0].obj=='usertype') this.clearChk(0);
		},
		'edit.username':function(newVal){
			if (this.chk[0].obj=='username') this.clearChk(0);
		},
		'edit.oldpassword':function(newVal){
			if (this.chk[0].obj=='oldpassword') this.clearChk(0);
		},
		'edit.newpassword':function(newVal){
			if (this.chk[0].obj=='newpassword' || this.chk[0].obj=='all') this.clearChk(0);
		},
		'edit.repassword':function(newVal){
			if (this.chk[0].obj=='repassword' || this.chk[0].obj=='all') this.clearChk(0);
		},
		'edit.pid':function(newVal){
			if (this.chk[0].obj=='department') this.clearChk(0);
		},
		'edit.jid':function(newVal){
			if (this.chk[0].obj=='job') this.clearChk(0);
		},
		'edit.realname':function(newVal){
			if (this.chk[0].obj=='realname') this.clearChk(0);
		},
		'edit.telnumber':function(newVal){
			if (this.chk[0].obj=='telnumber') this.clearChk(0);
		}
	}
});

var dialog=relaxDialog();
var ajax=new relaxAJAX();
ajax.before=function(){
	vu.load=true;
	vu.setChk(0,'loading','正在提交数据，请稍等...');
};
ajax.error=function(code,msg){
	vu.load=false;
	vu.setChk(0,'alert',code+' '+msg);
};
ajax.success=function(data){
	if (data.code){
		vu.load=false;
		vu.setChk(0,'alert',data.code+' '+data.message);
	}else{
		var msg;
		if (vu.op=='add'){
			msg='用户 '+vu.edit.username+' 已经添加成功！';
			vu.list.push({
				uid: data.data,
				username: vu.edit.username,
				tid: vu.edit.tid,
				tname: vu.usertype[vu.edit.tid].tname,
				pid: vu.edit.pid,
				pname: vu.edit.pid==''? '':vu.department[vu.edit.pid].pname,
				jid: vu.edit.jid,
				jname: vu.edit.jid==''? '':vu.job[vu.edit.jid].jname,
				realname: vu.edit.realname,
				telnumber: vu.edit.telnumber
			});
			vu.relist[data.data]=vu.list.length-1;
		}else if (vu.op=='edit'){
			msg='用户 '+vu.edit.username+' 资料已经修改成功！';
			vu.list[vu.relist[vu.edit.uid]].realname=vu.edit.realname;
			vu.list[vu.relist[vu.edit.uid]].telnumber=vu.edit.telnumber;
			if (vu.edit.uid==vu.me.uid){
				vu.me.realname=vu.edit.realname;
				vu.me.telnumber=vu.edit.telnumber;
			}
		}else{
			msg='用户 '+vu.list[vu.relist[vu.edit.uid]].username+' 已经成功删除！';
			vu.list.splice(vu.relist[vu.edit.uid],1);
			vu.relist={};
			for (var i=0; i<vu.list.length; i++){
				vu.relist[vu.list[i].uid]=i;
			}
		}
		vu.setChk(0,'ok',msg);
		setTimeout(function(){
			vu.clearChk(0);
			vu.load=false;
			vu.hideDialog();
		},2000);
	}
};

