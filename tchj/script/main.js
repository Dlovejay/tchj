//主界面+当前用户资料修改
var vu=new Vue({
  el:'#app',
  data:{
		url: URL,
		me: JSON.parse($('#account').text()),
		cfg: CFG,
		nowSel: 0,
		edit:{
			uid:'',
			oldpassword:'',
			newpassword:'',
			repassword:'',
			realname:'',
			telnumber:''
		},
		chk:'',
    load: false
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
		showDialog:function(txt){
			this.edit.realname=this.me.realname;
			this.edit.telnumber=this.me.telnumber;
			dialog.open(txt);
		},
		hideDialog:function(txt){
			this.edit.oldpassword='';
			this.edit.newpassword='';
			this.edit.repassword='';
			this.edit.realname='';
			this.edit.telnumber='';
			this.clearChk(0);
			dialog.close(txt);
		},
		setSel:function(index){
			this.nowSel=index;
		},
		sendEdit:function(){
			//检查规范
			var msg='',obj='';
			for (var i=0; i<1; i++){
				if (this.me.tid!=this.cfg.UM){
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
						if (REG.upwd.test(this.edit.repassword)===false){
							msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
							obj='repassword';
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
					if (this.edit.repassword!='' && REG.upwd.test(this.edit.repassword)===false){
						msg='密码为5~30个英文或者数字组成的字符串，可包含特殊字符-+#@%';
						obj='repassword';
						break;
					}
				}
				//密码逻辑
				if (this.edit.newpassword!==this.edit.repassword){
					msg='输入的密码与确认密码不匹配';
					obj='all';
					break;
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
				if (this.edit.newpassword=='' && this.edit.realname==this.me.realname && this.edit.telnumber==this.me.telnumber){
					msg='没有对用户信息做任何修改，无需提交';
					break;
				}
			}
			if (msg!=''){
				this.setChk(0,'warning',msg,obj);
				return false;
			}
			ajax.data={
				uid: this.edit.uid,
				oldpassword: this.edit.oldpassword? md5(this.edit.oldpassword):'',
				newpassword: this.edit.newpassword? md5(this.edit.newpassword):'',
				realname: this.edit.realname,
				telnumber: this.edit.telnumber
			}
			ajax.send();
		}
	},
	created:function(){
		this.nowSel=4;  //默认页面为业务概览
		this.chk=this._getChkobj(1);
		this.me.pname=this.me.pname || '';
		this.me.job=this.me.job || '';
		this.me.realname=this.me.realname || '';
		this.me.telnumber=this.me.telnumber || '';
		this.edit.uid=this.me.uid;
	},
	watch:{
		//输入项变更则取消错误提示信息
		'edit.oldpassword':function(newVal){
			if (this.chk[0].obj=='oldpassword') this.clearChk(0);
		},
		'edit.newpassword':function(newVal){
			if (this.chk[0].obj=='newpassword' || this.chk[0].obj=='all') this.clearChk(0);
		},
		'edit.repassword':function(newVal){
			if (this.chk[0].obj=='repassword' || this.chk[0].obj=='all') this.clearChk(0);
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
ajax.url=URL.useredit;
ajax.before=function(){
	vu.load=true;
	vu.setChk(0,'loading','正在提交修改，请稍等...');
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
		vu.load=false;
		vu.setChk(0,'ok','当前用户的信息修改成功');
		vu.me.realname=vu.edit.realname;
		vu.me.telnumber=vu.edit.telnumber;
		setTimeout(function(){
			vu.hideDialog('accountedit');
		},800);
	}
};