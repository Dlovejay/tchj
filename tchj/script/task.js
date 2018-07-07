var vu=new Vue({
	el: "#app",
	data:{
		cfg:CFG,
		user: JSON.array2Object(JSON.parse($("#user").text()),'uid'),
		department: JSON.array2Object(JSON.parse($("#department").text()),'pid'),
		me: JSON.parse($("#account").text()),
		op:'',    //操作类型 add添加操作 edit修改操作
		ajaxtype:'', //当前ajax合并未一个ajax对象，不同的ajax操作类型记录到ajaxtype
		list:[],  //任务列表信息
		refList:{}, //mid对照表,
		returnList:{},  //记录任务回复的对象 以任务mid编号作为索引
		isPublic:false, //是否可以发布任务
		isLeader:false, //是否是领导
		pager:{         //分页对象
			total:0,
			page:1,
			pagecount:0,
			pagesize:CFG.pagesize
		},
		real:{   //临时存储用户输入的查询条件
			status:'',
			is_timeout:'',
			keywords:'',
			start_at:'',
			end_at:''
		},
		realbak:{  //查询条件
			status:'',
			is_timeout:'',
			keywords:'',
			start_at:'',
			end_at:''
		},
		edit:{
			mid:'',
			title:'',
			mtitle:'',
			start_at:'',
			end_at:'',
			content:'',
			department:[],
			annex:[],
			isAll:1  //0全部 1自定义，实际不会传递给服务端
		},
		axTemp:{  //临时附件列表
			index:'',
			type:'1',
			url:'',
			name:''
		},
		answer:{  //回复相关
			do:'FINISHED',
			content:'',
			cause:''
		},
		chk:'',  //提示信息 [0]列表 [1]添加，编辑 [2]附件操作 [3]回复列表获取 [4]回复发送
		load:{
			list:false,  //显示列表加载中标记
			op:false,    //操作任务信息加载中标记
			re:false     //回复加载时候的标记
		},
		viewobj:''   //当前查看任务的对应对象
	},
	computed:{
		getDispatchDepartment: function(){ //获得可用于指派部门可选项
			var tempArray=[];
			var check;
			if (this.isLeader){
				check=['1','2'];
			}else{
				check=['2'];
			}
			for (var x in this.department){
				if (check.indexOf(this.department[x].plevel)>=0) tempArray.push(this.department[x]);
			}
			return tempArray;
		},
		getPublicDepartment: function(){   //获得发布部门可选项
			var tempArray=[];
			for (var x in this.department){
				if (this.isLeader && this.department[x].plevel=='0'){
					tempArray.push(this.department[x]);
					break;
				}else{
					if (this.department[x].plevel=='1') tempArray.push(this.department[x]);
				}
			}
			return tempArray;
		},
		canDo: function(){  //回复/修改操作是否可进行的基本判定
			if (this.load.re) return false;
			if (!this.viewobj) return false;
			if (!this.returnList[this.viewobj.mid]) return false;
			return true;
		},
		canRepeal: function(){  //是否可以撤销
			//"DELETE","EDIT","RECEIVE","REPLY","FINISHED","BACK","REPEAL"
			if (this.returnList[this.viewobj.mid].status.indexOf('REPEAL')>=0) return true;
			return false;
		},
		canDelete: function(){   //是否可以删除
			if (this.returnList[this.viewobj.mid].status.indexOf('DELETE')>=0) return true;
			return false;
		},
		canReply: function(){    //是否可以回复
			if (this.returnList[this.viewobj.mid].status.indexOf('REPLY')>=0) return true;
			if (this.returnList[this.viewobj.mid].status.indexOf('FINISHED')>=0) return true;
			return false;
		},
		canReceive: function(){  //是否可以接受任务
			if (this.returnList[this.viewobj.mid].status.indexOf('RECEIVE')>=0) return true;
			return false;
		},
		canEdit: function(){  //是否可以修改
			return this.returnList[this.viewobj.mid].canEdit;
		},
		canFinish: function(){ //是否可以完成
			if (this.returnList[this.viewobj.mid].status.indexOf('FINISHED')>=0) return true;
			return false;
		},
		canTimeout: function(){  //是否要填写超时说明
			if (this.me.pid!=this.viewobj.mtitle && this.returnList[this.viewobj.mid].timeout) return true;
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
			var d1=formatDateTime(getStringTime(obj['start_at']*1000),'date','-',true);
			var d2=formatDateTime(getStringTime(obj['end_at']*1000),'date','-',true);
			var dt1=d1.split('-');
			var dt2=d2.split('-');
			var dtt1=formatDateTime(d1,'date','c');
			var dateStr=dtt1;
			if (dt2[0]===dt1[0]){
				dateStr+='~'+dt2[1]+'月'+dt2[2]+'日';
			}else{
				dateStr+='~'+formatDateTime(d2,'date','c');
			}
			
			var pubDepartment=this.department[obj.initiate_pid].pname;
			var tempAuthor=this.user[obj['create_user_id']]['username'];
			var tempArray2=obj['departments'].split(',');
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
			var tempannex=obj['annex'];
			return {
					mid: obj.mid,
					title: obj.title,
					pub: pubDepartment,
					mtitle: obj.initiate_pid,
					start_at: d1,
					end_at: d2,
					date: dateStr,
					author: tempAuthor,
					authorid: obj['create_user_id'],
					department: temppass,
					departmentid: tempArray2,
					annex: tempannex.length,
					annexobj: tempannex,
					count: obj.count,
					status: obj.status,
					timeout: obj['is_timeout'],
					content: obj.content,
					cause: obj.cause
			};
		},
		setChk: function(index,flag,msg,obj){  //设置提示信息
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
		clearChk: function(index){  //清除提示信息
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
					if (!this.returnList[this.viewobj.mid]) this.getAJAXDetail(); //获取反馈列表
					break;
				case 'taskop':
					if (this.viewobj!==''){
						this.op='edit';
						this.edit.mid=this.viewobj.mid;
						this.edit.title=this.viewobj.title;
						this.edit.mtitle=this.viewobj.mtitle;
						this.edit.start_at=this.viewobj.start_at;
						this.edit.end_at=this.viewobj.end_at;
						this.edit.content=this.viewobj.content;
						this.edit.department=this.viewobj.departmentid;
						this.edit.annex=this.viewobj.annexobj;
						if (this.viewobj.departmentid.length==1 && this.viewobj.departmentid[0]=='0'){
							this.edit.isAll=0;
						}else{
							this.edit.isAll=1;
						}
					}else{
						this.op='add';
						this.edit.start_at=formatDateTime(new Date(),'date','-',true);
						this.edit.mtitle=this.me.pid;
					}
					break;
				case 'sure':
					var content;
					switch(index){
						case 'RECEIVE':
							content={content:'是否确认 <strong>接受</strong> 当前任务？'};
							break;
						case 'DELETE':
							content={content:'是否确认 <strong>删除</strong> 当前任务？'};
							break;
						case 'REPEAL':
							content={content:'是否确认 <strong>撤销</strong> 当前任务？'};
							break;
					}
					this.ajaxtype=index;
					dialog.open('sure',content);
					return;
					break;
				case 'answer':
					this.answer.content='';
					this.answer.cause='';
					this.answer.do='FINISHED';
					var nowstatus=this.returnList[this.viewobj.mid].status;
					if (nowstatus.indexOf('REPLY')>=0){
						this.ajaxtype='REPLY';
					}else{
						this.ajaxtype='FINISHED';
					}
					break;
				case 'selreal':
					for (var x in this.real){
						this.real[x]=this.realbak[x];
					}
					break;
			}
			dialog.open(txt);
		},
		hideDialog: function(txt){  //隐藏弹出窗口
			switch(txt){
				case 'filelist':
					for (var x in this.axTemp){
						this.axTemp[x]='';
					}
					this.axTemp['type']='1';
					this.clearChk(2);
					break;
				case 'taskop':
					for (var x in this.edit){
						this.edit[x]='';
					}
					this.edit.department=[];
					this.edit.annex=[];
					this.edit.isAll=1;
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
		AJAXbefore: function(){
			switch(this.ajaxtype){
				case 'list':
					this.load.list=true;
					this.setChk(0,'loading','正在加载任务列表，请稍等...');
					break;
				case 'add':
					this.load.op=true;
					this.setChk(1,'loading','正在提交任务信息，请稍等...');
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
						this.setTaskSend(data.data);
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
		getFileURL: function(obj){  //加工url参数
			if (obj.type=='1'){
				return this.ftp+obj.url;
			}else{
				return obj.url;
			}
		},
		annexOP: function(op,index){  //操作附件
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
		getAJAXList: function(pagenum){  //获得任务列表信息
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
				is_timeout: this.realbak.is_timeout,
				page: this.pager.page,
				pagesize: this.pager.pagesize
			},
			ajax.url=URL.tasklist;
			ajax.send();
		},
		setAJAXList: function(data){   //处理任务列表返回信息
			this.pager.page=data.pager.page;
			this.pager.total=data.pager.total;
			this.pager.pagecount=Math.ceil(this.pager.total/this.pager.pagesize);
			
			this.list=[];
			this.reflist={};
			this.returnList={};
			if (data.pager.total!=='0'){
				this.reflist={};
				for (var i=0; i<data.list.length; i++){
					this.list.push(this._processList(data.list[i]));
					this.reflist[data.list[i].mid]=i;
				}
				if (this.viewobj){
					this.viewobj=this.list[this.reflist[this.viewobj.mid]];
				}
				vu.clearChk(0);
			}else{
				vu.setChk(0,'warning','没有找到对应的任务信息');
			}
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
		//发送添加/编辑数据
		getTaskSend: function(){
			//检查相关项
			if (REG.title.check(this.edit.title)==false){
				this.setChk(1,'warning','请填写任务标题','title');
				return;
			}
			if (rexReg.date.test(this.edit.start_at)==false){
				this.setChk(1,'warning','开始日期不能为空或者日期格式有误','start_at');
				return;
			}
			if (rexReg.date.test(this.edit.end_at)==false){
				this.setChk(1,'warning','截止日期不能为空或者日期格式有误','end_at');
				return;
			}
			if (this.edit.content==''){
				this.setChk(1,'warning','请填写任务说明','content');
				return;
			}
			if (this.edit.department.length==0){
				this.setChk(1,'warning','任务没有指派给任何部门','department');
				return;
			}
			//逻辑验证
			var nowdate=formatDateTime(new Date(),'date','-',true);
			if (this.edit.end_at<=nowdate){
				this.setChk(1,'warning','请把截止日期设置为当天之后的日期','end_at');
				return;
			}
			if (this.edit.start_at>this.edit.end_at){
				this.setChk(1,'warning','截止日期要大于开始日期','end_at');
				return;
			}
			if (this.edit.annex.length==0){
				if (!window.confirm('当前任务没有包含任务附件文件，是否仍然要提交？')) return;
			}
			//标记发布部门
			if (this.isLeader && this.edit.isAll==0) this.edit.department=[0];
			this.ajaxtype='add';
			if (this.op=='add'){
				ajax.url=URL.taskadd;
			}else{
				ajax.url=URL.taskedit;
			}
			ajax.data={
				id: this.edit.mid,
				title:this.edit.title,
				initiate_pid:this.edit.mtitle,
				start_at:getUnixTime(this.edit.start_at)/1000,
				end_at:getUnixTime(this.edit.end_at)/1000,
				content:this.edit.content,
				departments:this.edit.department.join(','),
				annex:JSON.stringify(this.edit.annex)
			};
			ajax.send();
		},
		setTaskSend: function(data){   //添加成功返回处理
			if (this.op=='add'){
				this.setChk(1,'ok','新任务已经添加成功');
				setTimeout(function(){
					vu.getAJAXList();
					vu.load.op=false;
					vu.hideDialog('taskop');
				},1500);
			}else{
				this.setChk(1,'ok','任务已修改成功');
				setTimeout(function(){
					//vu.getAJAXList(vu.pager.page); 目前修改会影响列表排序，所以重新加载列表
					vu.getAJAXList();
					vu.load.op=false;
					vu.hideDialog('taskop');
				},1500);
			}
		},
		getAJAXDetail: function(){ //获取任务反馈信息
			delete this.returnList[this.viewobj.mid];
			this.ajaxtype='detail';
			ajax.data={
				id:this.viewobj.mid
			};
			ajax.url=URL.taskdetail;
			ajax.send();
		},
		setAJAXDetail: function(data){
			this.load.re=false;
			this.clearChk(3);
			var temp={
				list:data.replys,
				status:data.do,
				timeout:data.is_timeout,
				canEdit:data.canEdit
			};
			for (var i=0; i<temp.list.length; i++){  //协调显示状态
				var fituser=this.user[temp.list[i]['create_user_id']];
				if (fituser){
					temp.list[i].create_user_name=fituser.username;
					if (fituser.uid==this.viewobj.authorid){
						temp.list[i].classstr='anmanager';
					}else{
						temp.list[i].classstr='anuser';
					}
				}else{
					temp.list[i].create_user_name='unknow';
					temp.list[i].classstr='anuser';
				}
				temp.list[i].update_at=getStringTime(temp.list[i].update_at*1000,'','-');
			}
			if (temp.list.length==0) this.setChk(3,'warning','暂无任何回复信息');
			Vue.set(this.returnList,this.viewobj.mid,temp);
			if (this.viewobj.status!=data.status){
				this.list[this.reflist[this.viewobj.mid]].status=data.status;
			}
		},
		getAJAXNext: function(){
			var sd={
				id:this.viewobj.mid,
				content:'',
				do:this.ajaxtype,
				cause:''
			};
			switch(this.ajaxtype){
				case 'RECEIVE':
					sd.content='接受任务';
					break;
				case 'DELETE':
					sd.content='删除任务';
					break;
				case 'REPEAL':
					sd.content='撤销任务';
					break;
				case 'REPLY':
					if (this.answer.content==''){
						this.setChk(4,'warning','请填写回复内容','content');
						return;
					}
					if (this.returnList[this.viewobj.mid].timeout && this.answer.cause==''){
						this.setChk(4,'warning','请填写超时原因','cause');
						return;
					}
					sd.content=this.answer.content;
					sd.cause=this.answer.cause;
					break;
				case 'FINISHED':
					if (this.answer.content==''){
						this.setChk(4,'warning','请填写回复内容','content');
						return;
					}
					sd.content=this.answer.content;
					sd.do=this.answer.do;
					break;
			}
			if (sd.cause=='') delete sd.cause;
			ajax.data=sd;
			ajax.url=URL.tasknext;
			ajax.send();
		},
		setAJAXNext: function(data){
			//刷新回复页面
			this.setChk(4,'ok','设置任务状态成功');
			if (this.ajaxtype=='DELETE'){ //删除任务的情况，直接刷新
				setTimeout(function(){
					vu.hideDialog('sure');
					vu.hideDialog('viewop');
					vu.getAJAXList();
				},1200);
			}else{
				//更新列表中对应任务的状态
				this.list[this.reflist[this.viewobj.mid]].status=data.status;
				setTimeout(function(){
					if (vu.ajaxtype=='FINISHED' || vu.ajaxtype=='REPLY'){
						vu.hideDialog('answer');
					}else{
						vu.hideDialog('sure');
					}
					vu.ajaxtype='';
					//刷新回复列表
					vu.getAJAXDetail();
				},1200);
			}
		}
	},
	created:function(){
		this.chk=this._getChkobj(5); //准备提示信息对象
		//判定当前用户权限
		if (this.me.tid==CFG.UL){
			this.isPublic=true;
			this.isLeader=true;
		}else if(this.me.tid==CFG.UU){
			this.isPublic=true;
		}
	},
	watch:{
		//指派部门变更
		'edit.isAll':function(newVal){
			if (newVal==1){
				this.edit.department=[];
			}else{
				this.edit.department=[];
				for (var x in this.getDispatchDepartment){
					this.edit.department.push(this.getDispatchDepartment[x].pid);
				}
			}
		},
		//输入项变更则取消错误提示信息
		'edit.title':function(newVal){
			if (this.chk[1].obj==='title') this.clearChk(1);
		},
		'edit.start_at':function(newVal){
			if (this.chk[1].obj==='start_at') this.clearChk(1);
		},
		'edit.end_at':function(newVal){
			if (this.chk[1].obj==='end_at') this.clearChk(1);
		},
		'edit.content':function(newVal){
			if (this.chk[1].obj==='content') this.clearChk(1);
		},
		'edit.department':function(newVal){
			if (this.chk[1].obj==='department') this.clearChk(1);
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
ajax.before=vu.AJAXbefore;
ajax.error=vu.AJAXerror;
ajax.success=vu.AJAXsuccess;
var dialog=relaxDialog();
vu.getAJAXList(); //自动获得列表信息