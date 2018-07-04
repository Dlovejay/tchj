var vu=new Vue({
	el: "#app",
	data:{
		cfg:CFG,
		user: JSON.array2Object(JSON.parse($("#user").text()),'uid'),
		department: JSON.array2Object(JSON.parse($("#department").text()),'pid'),
		me: JSON.parse($("#account").text()),
		op:'',    //操作类型 add添加操作 edit修改操作
		ftp:'ftp://192.168.1.104:21/',
		list:[
			{cid:1,title:'请示关于查看户口信息权限',datemake:'2018-04-05',content:'请示关于查看户口信息权限请示关于查看户口信息权限请示关于查看户口信息权限',authorid:2,author:'user',annex:[{type:1,url:'search.doc',name:'相关文件'}],rcount:0,status:0},
			{cid:2,title:'调用后勤车队负责国庆事宜',datemake:'2018-06-21',content:'请示关于查看户口信息权限请示关于查看户口信息权限请示关于查看户口信息权限',authorid:2,author:'user',annex:[{type:1,url:'search.doc',name:'相关文件'}],rcount:1,status:1},
			{cid:3,title:'海上升明月，请示共此时',datemake:'2018-06-15',content:'请示关于查看户口信息权限请示关于查看户口信息权限请示关于查看户口信息权限',authorid:2,author:'user',annex:[{type:1,url:'search.doc',name:'相关文件'}],rcount:2,status:2}
		],
		reflist:{'1':0,'2':1,'3':2}, //mid对照表,
		returnList:{  //记录返回信息
			'1':[],
			'2':[{uid:3,username:'manager',datemake:'2018-06-22',content:'接受该请示',classstr:'anmanager'}],
			'3':[{uid:3,username:'manager',datemake:'2018-06-22',content:'接受该请示',classstr:'anmanager'},{uid:3,username:'manager',datemake:'2018-06-20',content:'哈哈，正有此意',classstr:'anmanager'}]
		},
		isPublic:false, //是否可以发布请示
		real:{   //实际用于提交服务端的查询条件
			status:'',
			key:'',
			pages:0, //共多少页
			total:0, //列表总数量
			nowpage:1, //当前页码
		},
		real2:{  //跟控件绑定的查询条件
			status:'',
			key:''
		},
		edit:{
			cid:'',
			title:'',
			content:'',
			annex:[]
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
		bs1: function(){  //检查回复
			if (this.viewobj){
				if (this.viewobj.status==1 && this.viewobj.authorid!=this.me.uid) return true;
			}
			return false;
		},
		bs3: function(){ //检查删除
			if (this.viewobj){
				if (this.viewobj.authorid==this.me.uid && this.viewobj.status==0) return true;
			}
			return false;
		},
		bs4: function(){ //检查接受
			if (this.viewobj && this.viewobj.status==0){
				if (this.viewobj.authorid!=this.me.uid) return true;
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
			}else if(txt=='inforOP'){
				if (this.viewobj!==''){
					this.op='edit';
					this.edit.cid=this.viewobj.cid;
					this.edit.title=this.viewobj.title;
					this.edit.content=this.viewobj.content;
					this.edit.annex=this.viewobj.annex;
				}else{
					this.op='add';
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
		//清除查询条件
		clearRealation: function(){
			for (var x in this.real2){
				this.real2[x]=this.real[x];
			}
		}
	},
	created:function(){
		this.chk=this._getChkobj(4); //准备提示信息对象
		//判定当前用户权限
		if (this.me.tid==CFG.UD){
			this.isPublic=true;
		}
	}
});

var ajax=new relaxAJAX();     //用于添加操作的ajax
ajax.before=function(){
	vu.AJAXbefore();
};
ajax.error=function(code,msg){
	vu.AJAXerror(code,msg);
};
ajax.success=function(data){
	vu.AJAXsuccess(data);
}
var dialog=relaxDialog();