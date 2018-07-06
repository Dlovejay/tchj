var vu=new Vue({
	el: "#app",
	data:{
		cfg:CFG,
		user: JSON.array2Object(JSON.parse($("#user").text()),'uid'),
		department: JSON.array2Object(JSON.parse($("#department").text()),'pid'),
		me: JSON.parse($("#account").text()),
		op:'',    //操作类型 add添加操作 edit修改操作
		ajaxtype:'', //当前ajax合并未一个ajax对象，不同的ajax操作类型记录到ajaxtype
		list:[],
		reflist:{}, //mid对照表,
		returnList:{},
		isPublic:false, //是否可以发布请示
		pager:{         //分页对象
			total:0,
			page:1,
			pagecount:0,
			pagesize:CFG.pagesize
		},
		real:{   //实际用于提交服务端的查询条件
			uid:'',
			keywords:'',
			status:'',
			start_date:'',
			end_date:''
		},
		realbak:{  //跟控件绑定的查询条件
			uid:'',
			keywords:'',
			status:'',
			start_date:'',
			end_date:''
		},
		edit:{
			cid:'',
			title:'',
			content:'',
			annex:[],
			pid:''
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
		getDispatchDepartment: function(){  //获得请示投递部门
			var tempArray=[];
			var check=['1'];
			for (var x in this.department){
				if (check.indexOf(this.department[x].plevel)>=0) tempArray.push(this.department[x]);
			}
			return tempArray;
		},
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
		_getChkobj: function(num){ //获得提示信息对象
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
		_processList: function(obj){  	//加工单条任务信息为可显示的内容
			return obj;
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
		showDialog: function(txt,index){ //显示弹出窗口
			switch(txt){
				case 'viewop':
					this.viewobj=this.list[this.reflist[index]];
					break;
				case 'consultop':
					if (this.viewobj!==''){
						this.op='edit';
						this.edit.cid=this.viewobj.cid;
						this.edit.title=this.viewobj.title;
						this.edit.content=this.viewobj.content;
						this.edit.annex=this.viewobj.annex;
					}else{
						this.op='add';
					}
					break;
			}
			dialog.open(txt);
		},
		hideDialog: function(txt){ //隐藏弹出窗口
			switch(txt){
				case 'filelist':
					for (var x in this.axTemp){
						this.axTemp[x]='';
					}
					this.axTemp['type']='1';
					this.clearChk(2);
					break;
				case 'consultop':
					for (var x in this.edit){
						this.edit[x]='';
					}
					this.edit.annex=[];
					this.op='';
					break;
				case 'viewop':
					this.viewobj='';
					break;
			}
			dialog.close(txt);
		},
		getFileURL: function(obj){ //加工url参数
			if (obj.type=='1'){
				return this.ftp+obj.url;
			}else{
				return obj.url;
			}
		},
		annexOP: function(op,index){ //操作附件
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
		AJAXbefore: function(){
			switch(this.ajaxtype){
				case 'list':
					this.load.list=true;
					this.setChk(0,'loading','正在加载请示列表，请稍等...');
					break;
				case 'add':
					this.load.op=true;
					this.setChk(1,'loading','正在提交请示信息，请稍等...');
					break;
				case 'detail':
					this.load.re=true;
					this.setChk(3,'loading','正在获取该任务回复列表，请稍等...');
					break;
				default:
					var tempArray=['RECEIVE','DELETE','REPEAL'];
					if (tempArray.indexOf(this.ajaxtype)>=0){
						this.load.re=true;
						this.setChk(4,'loading','正在重置任务状态，请稍等...');
					}else{
						tempArray=['REPLY','FINISHED'];
						if (tempArray.indexOf(this.ajaxtype)>=0){
							this.load.re=true;
							this.setChk(4,'loading','正在提交回复，请稍等...');
						}
					}
			}
		},
		AJAXerror: function(code,msg){
			switch(this.ajaxtype){
				case 'list':
					this.load.list=false;
					this.setChk(0,'alert',code+' '+msg);
					break;
				case 'add':
					this.load.op=false;
					this.setChk(1,'alert',code+' '+msg);
					break;
				case 'detail':
					this.load.re=false;
					this.setChk(3,'alert',code+' '+msg);
					break;
				default:
					var tempArray=['RECEIVE','DELETE','REPEAL','REPLY','FINISHED'];
					if (tempArray.indexOf(this.ajaxtype)>=0){
						this.load.re=false;
						this.setChk(4,'alert',code+' '+msg);
					}
					break;
			}
		},
		AJAXsuccess: function(data){
			switch(this.ajaxtype){
				case 'list':
					this.load.list=false;
					if (data.code){
						this.setChk(0,'alert',data.message);
					}else{
						this.setAJAXList(data.data);
					}
					break;
				case 'add':
					if (data.code){
						this.load.op=false;
						this.setChk(1,'alert',data.message);
					}else{
						this.setAJAXAdd(data.data);
					}
					break;
				case 'detail':
					if (data.code){
						this.load.re=false;
						this.setChk(3,'alert',data.message);
					}else{
						this.setAJAXDetail(data.data);
					}
					break;
				default:
					var tempArray=['RECEIVE','DELETE','REPEAL','REPLY','FINISHED'];
					if (tempArray.indexOf(this.ajaxtype)>=0){
						if (data.code){
							this.load.re=false;
							this.setChk(4,'alert',data.message);
							return;  //不能执行清空ajaxtype，否则继续提交会有问题
						}else{
							this.setAJAXNext(data.data);
							return;
						}
					}
			}
			this.ajaxtype='';
		},
		clearRealation: function(){ //清除查询条件
			for (var x in this.real2){
				this.real2[x]=this.real[x];
			}
		},
		getAJAXList: function(pagenum){   //获取请示列表
			if (pagenum===undefined){
				this.pager.total=0;
				this.pager.page=1;
				this.pager.pagecount=0;
			}else{
				this.pager.page=pagenum;
			}
			this.ajaxtype='list';
			
			ajax.data={
				keywords: this.realbak.keywords,
				status: this.realbak.status,
				page: this.pager.page,
				pagesize: this.pager.pagesize
			},
			ajax.url=URL.consultlist;
			ajax.send();
		},
		setAJAXList: function(data){
			//this.pager.page=data.pager.page;
			this.pager.total=data.length;
			this.pager.pagecount=Math.ceil(this.pager.total/this.pager.pagesize);
			
			this.list=[];
			this.reflist={};
			this.returnList={};
			if (this.pager.total!==0){
				this.reflist={};
				for (var i=0; i<data.list.length; i++){
					this.list.push(this._processList(data.list[i]));
					this.reflist[data.list[i].cid]=i;
				}
				if (this.viewobj){
					this.viewobj=this.list[this.reflist[this.viewobj.cid]];
				}
				vu.clearChk(0);
			}else{
				vu.setChk(0,'warning','没有找到对应的请示信息');
			}
		},
		getAJAXAdd: function(){  //提交添加
			//检查相关项
			if (REG.title.check(this.edit.title)==false){
				this.setChk(1,'warning','请填写请示标题','title');
				return;
			}
			if (this.edit.content==''){
				this.setChk(1,'warning','请填写请示说明','content');
				return;
			}
			if (this.edit.pid==''){
				this.setChk(1,'warning','请选择请示需要投递给哪个上级部门');
				return;
			}
			this.ajaxtype='add';
			ajax.url=URL.consultadd;
			//标记发布部门
			ajax.data={
				id: this.edit.cid,
				title:this.edit.title,
				content:this.edit.content,
				annex:JSON.stringify(this.edit.annex),
				pid:this.edit.pid
			};
			ajax.send();
		},
		setAJAXAdd: function(data){ //处理添加返回
			console.log(data);
		}
	},
	created:function(){
		this.chk=this._getChkobj(4); //准备提示信息对象
		//判定当前用户权限
		if (this.me.tid==CFG.UD) this.isPublic=true;
	},
	watch:{ //输入项变更则取消错误提示信息
		'edit.title':function(newVal){
			if (this.chk[1].obj==='title') this.clearChk(1);
		},
		'edit.content':function(newVal){
			if (this.chk[1].obj==='content') this.clearChk(1);
		},
		'axTemp.url':function(newVal){
			this.setChk(2);
		},
		'answer.content':function(newVal){
			if (this.chk[4].obj==='content') this.clearChk(4);
		},
		'answer.cause':function(newVal){
			if (this.chk[4].obj==='cause') this.clearChk(4);
		},
		'real.keywords':function(newVal){
			if (this.chk[1].obj=='keywords') this.clearChk(1);
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
vu.getAJAXList();