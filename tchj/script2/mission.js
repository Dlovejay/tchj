var vu=new Vue({
	el: "#app",
	data:{
		user: JSON.array2Object(JSON.parse($("#user").text()),'uid'),
		department: JSON.array2Object(JSON.parse($("#department").text()),'pid'),
		me: JSON.parse($("#account").text()),
		ftp:'ftp://192.168.1.104:21/',
		op:'',    //操作类型 add添加操作 edit修改操作
		list:[],  //任务列表信息
		reflist:{}, //mid对照表,
		returnList:{},  //记录任务回复的对象 以任务mid编号作为索引
		isPublic:false, //是否可以发布任务
		isChoose:false, //分派任务时是否可以选择全部
		real:{   //实际用于提交服务端的查询条件
			status:'',
			timeout:'',
			key:'',
			datestart:'',
			dateend:'',
			pages:0, //共多少页
			total:0, //列表总数量
			nowpage:1, //当前页码
		},
		real2:{  //跟控件绑定的查询条件
			status:'',
			timeout:'',
			key:'',
			datestart:'',
			dateend:''
		},
		edit:{
			mid:'',
			title:'',
			tips:'',
			datestart:'',
			dateend:'',
			intro:'',
			power:[],
			annex:[],
			powertype:1 //记录分配类型 0全部 1自定义 该属性仅用于领导账号
		},
		axTemp:{  //临时附件列表
			index:'',
			type:'1',
			url:'',
			name:''
		},
		chk:'',  //提示信息 [0]列表 [1]添加，编辑 [2]附件操作 [3]回复操作
		load:{
			list:false,  //显示列表加载中标记
			op:false,    //操作任务信息加载中标记
			re:false     //回复加载时候的标记
		},
		viewobj:''   //当前查看任务的对应对象
	},
	computed:{
		//获得可用于指派的部门信息
		getDpmt: function(){
			var tempArray=[];
			for (var x in this.department){
				if (this.department[x].plevel=='2') tempArray.push(this.department[x]);
			}
			return tempArray;
		},
		bs1: function(){  //检查回复
			if (this.viewobj){
				if (this.viewobj.status==2 || this.viewobj.status==5){
					if (this.viewobj.passid=='0'){
						if (this.viewobj.authorid!=this.me.uid) return true;
					}else{
						var temp=this.viewobj.passid.split(',');
						if (temp.indexOf(this.me.pid+'')>=0) return true;
					}
				}else if(this.viewobj.status==3){
					if (this.me.uid==this.viewobj.authorid) return true;
				}
			}
			return false;
		},
		bs2: function(){ //检查撤销
			if (this.viewobj){
				if (this.viewobj.authorid==this.me.uid && (this.viewobj.status==2 || this.viewobj.status==3 || this.viewobj.status==5)) return true;
			}
			return false;
		},
		bs3: function(){ //检查删除
			if (this.viewobj){
				if (this.viewobj.authorid==this.me.uid && (this.viewobj.status==0 || this.viewobj.status==1)) return true;
			}
			return false;
		},
		bs4: function(){ //检查接受
			if (this.viewobj && this.viewobj.status==1){
				if (this.viewobj.passid=='0'){
					if (this.viewobj.authorid!=this.me.uid) return true;
				}else{
					var temp=this.viewobj.passid.split(',');
					if (temp.indexOf(this.me.pid+'')>=0) return true;
				}
			}
			return false;
		}
	},
	methods:{
		//获得提示信息对象
		_getChkobj: function(num){
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
		//设置提示信息
		setChk: function(index,flag,msg,obj){
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
		//清除提示信息
		clearChk: function(index){
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
		//显示弹出窗口
		showDialog: function(txt,index){
			if (txt=='viewOP'){
				this.viewobj=this.list[this.reflist[index]];
				if (!this.returnList[this.viewobj.mid]) this.getReturnlist(); //获取反馈列表
			}else if(txt=='inforOP'){
				if (this.viewobj!==''){
					this.op='edit';
					this.edit.mid=this.viewobj.mid;
					this.edit.title=this.viewobj.mtitle;
					this.edit.tips=this.viewobj.tips;
					this.edit.datestart=this.viewobj.datestart;
					this.edit.dateend=this.viewobj.dateend;
					this.edit.intro=this.viewobj.content;
					if (this.viewobj.passid=='0'){
						this.edit.power=[];
					}else{
						this.edit.power=this.viewobj.passid.split(',');
					}
					this.edit.annex=this.viewobj.annexobj;
					this.edit.powertype=this.viewobj.powertype;
				}else{
					this.op='add';
					this.edit.datestart=formatDateTime(new Date(),'date','-',true);
				}
			}
			dialog.open(txt);
		},
		//隐藏弹出窗口
		hideDialog: function(txt){
			if (txt=='filelist'){
				for (var x in this.axTemp){
					this.axTemp[x]='';
				}
				this.axTemp['type']='1';
				this.clearChk(2);
			}else if(txt=='inforOP'){
				for (var x in this.edit){
					this.edit[x]='';
				}
				this.edit.power=[];
				this.edit.annex=[];
				this.edit.powertype=1;
				this.op='';
			}else if(txt=='viewOP'){
				this.viewobj='';
			}
			dialog.close(txt);
		},
		//加工url参数
		getFileURL: function(obj){
			if (obj.type=='1'){
				return this.ftp+obj.url;
			}else{
				return obj.url;
			}
		},
		//操作附件
		annexOP: function(op,index){
			if (op=='del'){  //删除附件
				this.edit.annex.splice(index,1);
			}else if (op=='edit' && this.edit.annex[index]){  //打开附件编辑窗口
				var temp=this.edit.annex[index];
				this.axTemp.index=index;
				this.axTemp.type=temp.type;
				this.axTemp.url=temp.url;
				this.axTemp.name=temp.name;
				this.showDialog('filelist');
			}else if(op=='add'){  //附件预添加操作
				if (this.axTemp.url==''){
					this.setChk(2,'warning','附件的文件路径不能为空','url');
					return;
				}
				if (REG.url.check(this.axTemp.url)==false){
					this.setChk(2,'warning','文件路径似乎不是一个有效的url地址','url');
					return;
				}
				if (this.axTemp.name==''){
					//自动生成name
					var tempArray=this.axTemp.url.split("/");
					var temp=tempArray[tempArray.length-1];
					this.axTemp.name=temp.replace(/\.[^\.]+/,'');
				}
				var temp={
					type: this.axTemp.type,
					url: this.axTemp.url,
					name: this.axTemp.name
				};
				if (typeof(this.axTemp.index)=='number'){
					this.edit.annex[this.axTemp.index]=temp;
				}else{
					this.edit.annex.push(temp);
				}
				this.hideDialog('filelist');
			}
		},
		//发送添加数据
		sendData: function(){
			//检查相关项
			//输入规范
			if (this.edit.title==''){
				this.setChk(1,'warning','请填写任务标题','title');
				return;
			}
			if (rexReg.date.test(this.edit.datestart)==false){
				this.setChk(1,'warning','开始日期不能为空或者日期格式有误','datestart');
				return;
			}
			if (this.edit.dateend!='' && rexReg.date.test(this.edit.dateend)==false){
				this.setChk(1,'warning','截止日期格式有误','dateend');
				return;
			}
			if (this.edit.intro==''){
				this.setChk(1,'warning','请填写任务说明','intro');
				return;
			}
			//协调任务分配数据
			if (this.edit.powertype!='0'){
				if (this.edit.power.length==0){
					this.setChk(1,'warning','任务没有指派给任何部门','power');
					return;
				}
			}
			//逻辑验证
			if (this.op=='add'){
				var nowdate=formatDateTime(new Date(),'date','-',true);
				if (this.edit.dateend!=''){
					if (this.edit.dateend<=nowdate){
						this.setChk(1,'warning','请把截止日期设置为当天之后的日期','dateend');
						return;
					}
					if (this.edit.datestart>this.edit.dateend){
						this.setChk(1,'warning','截止日期要大于开始日期','dateend');
						return;
					}
				}
			}else{
				
			}
			if (this.edit.annex.length==0){
				if (!window.confirm('当前任务没有包含任务附件文件，是否仍然要提交？')) return;
			}
			ajax.data=this.edit;
			ajax.send();
		},
		//清除查询条件
		clearRealation: function(){
			for (var x in this.real2){
				this.real2[x]=this.real[x];
			}
		},
		//获得任务列表信息
		getListData: function(){
			//检查输入项
			if (this.real2.key!=''){
				var regKey=/^[0-9a-zA-Z中文]+$/;
				if (regKey.check(this.real2.key)==false){
					dialog.open('msgshow',{content:'关键字格式有误，请不要包含特殊字符'});
					return;
				}
			}
			for (var x in this.real2){
				this.real[x]=this.real2[x];
			}
			this.real.pages=0;
			this.real.total=0;
			this.real.nowpage=1;
			ajaxlist.data=this.real;
			ajaxlist.send();
		},
		//点击分页事件
		gopage: function(pagenum){
			if (pagenum===undefined){
			}else{
				if (pagenum==this.real.nowpage) return;
			}
			this.real.nowpage=pagenum;
			ajaxlist.data=this.real;
			ajaxlist.send();
		},
		//加工任务信息为可显示的内容
		processList: function(obj){
			var d1=obj['datestart'].split('-');
			var d2=obj['dateend'].split('-');
			var tempStr1=d1[0]+'年'+d1[1]+'月'+d1[2]+'日~';
			var tempStr2='';
			if (d2.length!=3 || d2[0]=='9999'){
				tempStr2='不限';
				obj['dateend']='';
			}else{
				if (d1[0]==d2[0]){
					tempStr2=d2[1]+'月'+d2[2]+'日';
				}else{
					tempStr2=d2[0]+'年'+d2[1]+'月'+d2[2]+'日';
				}
			}
			var tempAuthor=this.user[obj['author']]['username'];
			var tempArray2=obj['pass'].split(',');
			var temppass=[];
			for (var j=0; j<tempArray2.length; j++){
				if (tempArray2[j]=='0'){
					temppass.push('全部');
					break;
				}else{
					temppass.push(this.department[tempArray2[j]].pname);
				}
			}
			temppass=temppass.join(',');
			if (temppass=='全部'){
				temptype='0';
			}else{
				temptype='1';
			}
			var tempannex=JSON.parse(obj['annex']);
			if (tempannex===null) tempannex='';
			return {
					mid: obj.mid,
					mtitle: obj.mtitle,
					datestart: obj.datestart,
					dateend: obj.dateend,
					date: tempStr1+tempStr2,
					author: tempAuthor,
					authorid: obj.author,
					pass: temppass,
					passid: obj.pass,
					annex: tempannex.length,
					annexobj: tempannex,
					rcount: obj.rcount,
					status: obj.status,
					timeout: obj.timeout,
					tips: obj.tips,
					content: obj.mcontent,
					powertype: temptype
			};
		},
		//获取任务反馈信息
		getReturnlist: function(){
			ajaxre.data='mid='+this.viewobj.mid;
			ajaxre.send();
		}
	},
	created:function(){
		this.chk=this._getChkobj(4); //准备提示信息对象
		//判定当前用户权限
		if (this.me.tid==CFG.UL || this.me.tid==CFG.UU){
			this.isPublic=true;
		}
		if (this.me.tid==CFG.UL){
			this.isChoose=true;
		}
	},
	watch:{
		//输入项变更则取消错误提示信息
		'edit.title':function(newVal){
			this.clearChk(1);
		},
		'edit.datestart':function(newVal){
			this.clearChk(1);
		},
		'edit.dateend':function(newVal){
			this.clearChk(1);
		},
		'edit.intro':function(newVal){
			this.clearChk(1);
		},
    'edit.power':function(newVal){
			if (newVal.length>0){
				this.setChk(1);
			}
		},
		'axTemp.url':function(newVal){
			this.setChk(2);
		}
	}
});
var ajaxlist=new relaxAJAX(); //获取列表的ajax
var ajax=new relaxAJAX();     //用于添加操作的ajax
var ajaxre=new relaxAJAX();   //获取回复信息的ajax

ajax.url=URL.missionadd;
ajaxlist.url=URL.missionlist;
ajaxre.url=URL.returnlist;

ajaxlist.before=function(){
	vu.load.list=true;
	vu.setChk(0,'loading','正在加载任务列表信息，请稍等...');
};
ajaxlist.error=function(code,msg){
	vu.load.list=false;
	vu.setChk(0,'alert',code+' '+msg);
};
ajaxlist.success=function(data){
	vu.load.list=false;
	if (data.code){
		vu.setChk(0,'alert',data.message);
	}else{
		//协调任务列表的显示
		var tempArray=[];
		vu.reflist={};
		for (var i=0; i<data.data.length; i++){
			tempArray.push(vu.processList(data.data[i]));
			vu.reflist[data.data[i].mid]=i;
		}
		vu.list=tempArray;
		vu.real.nowpage=parseInt(data.nowpage);
		vu.real.total=parseInt(data.allcount);
		vu.real.pages=Math.ceil(vu.real.total/CFG.cut);
		if (tempArray.length==0){
			vu.setChk(0,'warning','没有找到对应的任务信息');
		}else{
			vu.clearChk(0);
		}
	}
};

ajax.before=function(){
	vu.load.op=true;
	vu.setChk(1,'loading','正在提交数据，请稍等...');
};
ajax.error=function(code,msg){
	vu.load.op=false;
	vu.setChk(1,'alert',code+' '+msg);
};
ajax.success=function(data){
	if (data.code){
		vu.load.op=false;
		vu.setChk(1,'alert',data.message);
	}else{
		if (vu.op=='add'){
			vu.setChk(1,'ok','新任务已经添加成功');
			vu.getListData();
		}else{
			vu.setChk(1,'ok','任务已修改成功');
			vu.gopage();
		}
		setTimeout(function(){
			vu.load.op=false;
			vu.hideDialog('inforOP');
		},2000);
	}
}

//用于回复信息获取的ajax
ajaxre.before=function(){
	vu.load.re=true;
	vu.setChk(3,'loading','正在加载该任务的回复列表...');
};
ajaxre.error=function(code,msg){
	vu.load.re=false;
	vu.setChk(3,'alert',code+' '+msg);
};
ajaxre.success=function(data){
	if (data.code){
		vu.load.re=false;
		vu.setChk(3,'alert',data.message);
	}else{
		vu.load.re=false;
		vu.clearChk(3);
		//增加用户名字段
		var temp=data.data;
		for (var i=0; i<temp.length; i++){
			var fituser=vu.user[temp[i].uid];
			if (fituser){
				temp[i].username=fituser.username;
				if (temp[i].uid==vu.viewobj.authorid){
					temp[i].classstr='anmanager';
				}else{
					temp[i].classstr='anuser';
				}
			}else{
				temp[i].username='未知';
				temp[i].classstr='anuser';
			}
			temp[i].username=vu.user[temp[i].uid].username;
		}
		Vue.set(vu.returnList,vu.viewobj.mid,temp);
		//vu.returnList[vu.viewobj.mid]=temp;
		//console.log(vu.returnList);
	}
};

var dialog=relaxDialog();
vu.getListData(); //自动获得列表信息