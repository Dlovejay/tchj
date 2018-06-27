var vu = new Vue({
  el: "#app",
  data: {
    uname: "",
    upwd: "",
		auto: true,
		cfg: CFG,
    regDic: {
      uname: {
        reg: REG.uname,
        message: "请正确填写用户名",
        chk: false
      },
      upwd: {
        reg: REG.upwd,
        message: "请正确填写登录密码",
        chk: false
      }
    },
    login: false,
    mtype: "",
    message: ""
  },
	computed: {
		tipClass: function(){
			switch(this.mtype){
				case "warning":
					return "fa-info-circle";
				case "error":
					return "fa-times-circle-o";
				case "ok":
					return "fa-check-circle-o";
				default:
					return "";
			}
		}
	},
  methods: {
    //验证登录信息
    checkSubmit: function(type){
      var checkArray = [];
      if (type) {
        checkArray.push(type);
      } else {
        checkArray = ['uname','upwd'];
      }
      var key;
      var flag=[];
      for (var i=0; i<checkArray.length; i++) {
        key=checkArray[i];
        if (this[key]!=="" && this.regDic[key]["reg"].test(this[key])===false){
          this.regDic[key]["chk"]=true;
          flag.push(key);
        }else{
          this.regDic[key]["chk"]=false;
        }
      }
      if (flag.length===1) {
        this.message=this.regDic[flag[0]]["message"];
        this.mtype="warning";
        return false;
      }else{
        flag=[];
        for (var x in this.regDic){
          if (this.regDic[x]["chk"]===true){
            flag.push(x);
          }
        }
        if (flag.length===0){
          this.message="";
          this.mtype="";
          return true;
        }else{
          if (flag.length===1){
            this.message=this.regDic[flag[0]]["message"];
          }else{
            this.message="请检查以下出错项目";
          }
          this.mtype = "warning";
          return false;
        }
      }
    },
    //登录判定
    doLogin: function(){
      if (this.login) return;
      if (this.checkSubmit()===false) return;
      if (this.uname==="" || this.upwd==="") {
        this.mtype="warning";
        this.message="请先把登录信息填写完整";
        return;
      }
      ajax.data = {
        username: this.uname,
        password: this.upwd,
        auto: this.auto?1:0
      };
      ajax.send();
    },
    //传送登录信息
    sendLogin: function () {
      this.login = true;
      this.mtype = "loading";
      this.message = "正在登录，请稍后......";
    },
    //ajax返回设置
    getReturn: function (data, code, msg){
      if (code!==undefined){
				this.login=false;
        this.mtype="error";
        this.message=msg;
      }else{
        if (data.code!==0){
					this.login=false;
          this.mtype="error";
          this.message=data.message;
        }else{
          this.mtype="ok";
          this.message="身份确认，登录成功，正在跳转请稍等...";
          setTimeout(function(){
						top.location.href =URL.main
					},1000);
        }
      }
    },
    //遍历到下一个输入框
    nextInput:function(index){
      $(".loginForm input").eq(index).focus();
    }
  },
  watch:{
    uname: function(){
      this.checkSubmit("uname");
    },
    upwd: function(){
      this.checkSubmit("upwd");
    }
  }
});

var ajax = new relaxAJAX();
ajax.url=URL.loginin;
ajax.before=vu.sendLogin;
ajax.error=function(code, msg){
  vu.getReturn("", code, msg);
};
ajax.success=function(data){
  vu.getReturn(data);
}