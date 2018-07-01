var vu = new Vue({
  el: "#app",
  data:{
		ver:CFG.ver,
		login:{
			username:'',
			password:'',
			auto:1
		},
		chk:'',
    load: false
  },
	computed:{
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
    //登录判定
    doLogin: function(){
      if (this.load) return;
			if (this.login.username==''){
				this.setChk(0,'warning','请填写用户名信息','username');
				return;
			}
			if (this.login.password==''){
				this.setChk(0,'warning','请填写登录密码信息','password');
				return;
			}
			if (REG.uname.test(this.login.username)==false){
				this.setChk(0,'warning','用户名只能是英文字符或者数字的组合','username');
				return;
			}
			if (REG.uname.test(this.login.password)==false){
				this.setChk(0,'warning','密码只能是英文字符，数字和特殊字符-+#@%的组合','password');
				return;
			}
      ajax.data={
				username: this.login.username,
				password: md5(this.login.password),
				auto: this.login.auto? this.login.auto:'0'
			};
      ajax.send();
    },
    //ajax返回设置
    getReturn: function(data){
      if (data.code){
				this.load=false;
				if (data.code='450'){  //约定好用户名密码错误
					this.setChk(0,'alert',data.message,'all');
				}else{
					this.setChk(0,'alert',data.message);
				}
      }else{
				this.setChk(0,'ok','登录成功，正在跳转请稍等...');
				setTimeout(function(){
					top.location.href=URL.home;
				},800);
      }
    },
    //遍历到下一个输入框
    nextInput:function(index){
      $(".loginForm input").eq(index).focus();
    }
  },
	created:function(){
		this.chk=this._getChkobj(1);
	},
  watch:{
		//输入项变更则取消错误提示信息
		'login.username':function(newVal){
			if (this.chk[0].obj=='username' || this.chk[0].obj=='all'){
				this.clearChk(0);
			}
		},
		'login.password':function(newVal){
			if (this.chk[0].obj=='password' || this.chk[0].obj=='all'){
				this.clearChk(0);
			}
		}
  }
});

var ajax = new relaxAJAX();
ajax.url=URL.loginin;
ajax.before=function(){
	vu.load=true;
	vu.setChk(0,'loading','正在提交登录信息，请稍等...');
};
ajax.error=function(code, msg){
	vu.load=false;
  vu.setChk(0,'alert',code+' '+msg);
};
ajax.success=function(data){
  vu.getReturn(data);
}