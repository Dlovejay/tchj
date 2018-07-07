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
		reflist:{}, //cid对照表,
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
		answer:{  //回复相关
			complete:1,
			content:''
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
		canDo: function(){  //回复/修改操作是否可进行的基本判定
			if (this.load.re) return false;
			if (!this.viewobj) return false;
			if (!this.returnList[this.viewobj.cid]) return false;
			return true;
		},
		canRepeal: function(){  //是否可以撤销
			var temp=this.viewobj;
			if (this.me.pid==temp.createpid && temp.status==1) return true;
			return false;
		},
		canDelete: function(){   //是否可以删除
			var temp=this.viewobj;
			if (this.me.pid==temp.createpid && temp.status=='0') return true;
			return false;
		},
		canReply: function(){    //是否可以回复
			var temp=this.viewobj;
			if (this.me.pid==temp.pid && temp.status==1) return true;
			return false;
		},
		canReceive: function(){  //是否可以接受任务
			var temp=this.viewobj;
			if (this.me.pid==temp.pid && temp.status=='0') return true;
			return false;
		},
		canEdit: function(){  //是否可以修改
			var temp=this.viewobj;
			if (this.me.pid==temp.createpid && temp.status=='0') return true;
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
			var dateStr=obj.create_date.split(' ')[0];
			var pname=this.department[obj.pid]? this.department[obj.pid].pname:'unknow';
			obj.annex=JSON.parse(obj.annex);
			return {
				cid: obj.id,
				title: obj.title,
				content: obj.content,
				uid: obj.uid,
				username: obj.username,
				annex: obj.annex,
				date: dateStr,
				status: obj.status,
				rcount: obj.rcount,
				pid: obj.pid,  //请示投递的部门
				pname: pname,
				createpid: obj.created_pid  //发布请示的用户所在的部门
			}
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
					if (!this.returnList[this.viewobj.cid]) this.getAJAXDetail(); //获取反馈列表
					break;
				case 'consultop':
					if (this.viewobj!==''){
						this.op='edit';
						this.edit.cid=this.viewobj.cid;
						this.edit.title=this.viewobj.title;
						this.edit.content=this.viewobj.content;
						this.edit.annex=this.viewobj.annex;
						this.edit.pid=this.viewobj.pid;
					}else{
						this.op='add';
					}
					break;
				case 'sure':
					var content;
					switch(index){
						case 'RECEIVE':
							content={content:'是否确认 <strong>受理</strong> 当前请示？'};
							break;
						case 'DELETE':
							content={content:'是否确认 <strong>删除</strong> 当前请示？'};
							break;
						case 'REPEAL':
							content={content:'是否确认 <strong>撤销</strong> 当前请示？'};
							break;
					}
					this.ajaxtype=index;
					dialog.open('sure',content);
					return;
				case 'answer':
					this.answer.content='';
					this.answer.complete=1;
					this.ajaxtype='REPLY';
					break;
				case 'selreal':
					for (var x in this.real){
						this.real[x]=this.realbak[x];
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
					this.clearChk(1);
					break;
				case 'viewop':
					this.viewobj='';
					this.clearChk(3);
					break;
				case 'sure':
					this.ajaxtype='';
					this.clearChk(4);
					break;
				case 'answer':
					this.ajaxtype='';
					this.clearChk(4);
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
						if (this.ajaxtype=='REPLY'){
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
					var tempArray=['RECEIVE','DELETE','REPEAL','REPLY'];
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
					var tempArray=['RECEIVE','DELETE','REPEAL','REPLY'];
					if (tempArray.indexOf(this.ajaxtype)>=0){
						if (data.code){
							this.load.re=false;
							this.setChk(4,'alert',data.message);
							return;  //不能执行清空ajaxtype，否则继续提交会有问题
						}else{
							if (this.ajaxtype=='RECEIVE' || this.ajaxtype=='REPLY'){
								this.setAJAXReply(data.data);
							}else{
								this.setAJAXDel(data.data);
							}
						}
					}
			}
			this.ajaxtype='';
		},
		clearRealation: function(){  //清除查询条件
			for (var x in this.real){
				this.real[x]=this.realbak[x];
			}
		},
		goSearch: function(){  //查询操作
			//检查规范
			if (this.real.keywords!='' && REG.title.check(this.real.keywords)==false){
				this.setChk(1,'warning','查询标题关键字填写不规范','keywords');
				return;
			}
			for (var x in this.real){
				this.realbak[x]=this.real[x];
			}
			this.getAJAXList();
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
				page_size: this.pager.pagesize
			},
			ajax.url=URL.consultlist;
			ajax.send();
		},
		setAJAXList: function(data){  //列表返回处理
			//this.pager.page=data.pager.page;
			this.pager.total=data.length;
			this.pager.pagecount=Math.ceil(this.pager.total/this.pager.pagesize);
			
			this.list=[];
			this.reflist={};
			this.returnList={};
			if (this.pager.total!==0){
				this.reflist={};
				for (var i=0; i<data.length; i++){
					this.list.push(this._processList(data[i]));
					this.reflist[data[i].id]=i;
				}
				if (this.viewobj){
					this.viewobj=this.list[this.reflist[this.viewobj.cid]];
				}
				vu.clearChk(0);
			}else{
				vu.setChk(0,'warning','没有找到对应的请示信息');
			}
		},
		getAJAXAdd: function(){  //添加请示提交
			//检查相关项
			if (REG.title.check(this.edit.title)==false){
				this.setChk(1,'warning','请填写请示标题或者标题填写包含了特殊字符','title');
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
			if (this.edit.annex.length==0){
				if (!window.confirm('当前请示没有包含任务附件文件，是否仍然要提交？')) return;
			}
			this.ajaxtype='add';
			if (this.op=='add'){
				ajax.url=URL.consultadd;
			}else{
				ajax.url=URL.consulteidt;
			}
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
			if (this.op=='add'){
				this.setChk(1,'ok','请示已经添加成功');
				setTimeout(function(){
					vu.getAJAXList();
					vu.load.op=false;
					vu.hideDialog('consultop');
				},1500);
			}else{
				this.setChk(1,'ok','请示已修改成功');
				setTimeout(function(){
					vu.getAJAXList();
					vu.load.op=false;
					vu.hideDialog('consultop');
				},1500);
			}
		},
		getAJAXDetail: function(){ //获取请示回复信息列表
			delete this.returnList[this.viewobj.cid];
			this.ajaxtype='detail';
			ajax.data={
				cid:this.viewobj.cid,
				page:1,
				pagesize:100
			};
			ajax.url=URL.consultreply;
			ajax.send();
		},
		setAJAXDetail: function(data){  //请示回复设置
			this.load.re=false;
			this.clearChk(3);
			var temp=[];
			for (var i=0; i<data.length; i++){  //协调显示状态
				temp[i]={};
				var returner=this.user[data[i]['uid']];
				if (returner){
					temp[i].username=returner.username;
					if (returner.uid==this.viewobj.uid){
						temp[i].classstr='anmanager';
					}else{
						temp[i].classstr='anuser';
					}
				}else{
					temp[i].username='unknow';
					temp[i].classstr='anuser';
				}
				temp[i].create_date=data[i].create_date;
				temp[i].content=data[i].content;
			}
			if (data.length==0) this.setChk(3,'warning','暂无任何回复信息');
			Vue.set(this.returnList,this.viewobj.cid,temp);
		},
		doSure: function(){
			switch(this.ajaxtype){
				case 'RECEIVE':
					this.answer.content='受理请示';
					this.answer.complete=0;
					this.getAJAXReply();
					break;
				case 'DELETE':
					this.getAJAXDel();
					break;
				case 'REPEAL':
					this.getAJAXDel();
					break;
				case 'REPLY':
					if (this.answer.content==''){
						this.setChk(4,'warning','请填写回复内容','content');
						return;
					}
					this.getAJAXReply();
					break;
			}
		},
		getAJAXReply: function(){  //发送请示回复
			ajax.data={
				cid: this.viewobj.cid,
				content: this.answer.content,
				complete: this.answer.complete
			};
			ajax.url=URL.consultanswer;
			ajax.send();
		},
		setAJAXReply: function(data){  //请示回复返回处理
			if (this.ajaxtype=='REPLY'){
				this.setChk(4,'ok','请示回复已发送成功');
				if (this.answer.complete=='1'){
					this.list[this.reflist[this.viewobj.cid]].status=2;
				}
			}else{
				this.setChk(4,'ok','已成功受理该请示');
				this.list[this.reflist[this.viewobj.cid]].status=1;
			}
			this.list[this.reflist[this.viewobj.cid]].rcount++;
			setTimeout(function(){
				vu.hideDialog('sure');
				vu.hideDialog('answer');
				//刷新回复列表
				vu.getAJAXDetail();
			},1500);
		},
		getAJAXDel: function(){  //删除请示
			ajax.url=URL.consultdel
			ajax.data={cid:this.viewobj.cid};
			ajax.send();
		},
		setAJAXDel: function(data){  //删除请示的返回处理
			if (this.ajaxtype=='DELETE'){
				this.setChk(4,'ok','请示信息已经成功删除');
				setTimeout(function(){
					vu.hideDialog('sure');
					vu.hideDialog('viewop');
					vu.getAJAXList();
				},1500);
			}else{
				this.setChk(4,'ok','请示信息已经成功撤销');
				this.list[this.reflist[this.viewobj.cid]].status=3;
				setTimeout(function(){
					vu.hideDialog('sure');
					vu.getAJAXDetail();
				},1500);
			}
		}
	},
	created:function(){
		this.chk=this._getChkobj(5);
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