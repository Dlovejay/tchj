var vu=new Vue({
  el:'#app',
  data:{
		list:{},  //pid索引的对象
		department:{},
		edit:{
			tid:'',
			pid:'',
			pname:'',
			jname:'',
			jname2:''
		},
		op:'',
		chk:'',
		load:false
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
		showDialog:function(optype,keynum){
			if (optype=='edit'){
				this.op='edit';
				this.editInit(this.list[keynum]);
				dialog.open('jobop');
			}else if(optype=='add'){
				this.op='add';
				this.edit.pid=keynum;
				dialog.open('jobop');
			}else{
				this.op='del';
				dialog.open('opsure',{content:'请确认是否删除职务 <strong>'+ this.edit.jname+'</strong> ?'});
			}
		},
		hideDialog:function(name){
			if (name=='jobop'){
				this.op='';
				for (var x in this.edit){
					this.edit[x]='';
				}
				this.clearChk(0);
			}else{
				this.op='edit';
			}
			dialog.close(name);
		},
		//初始化edit数据
		editInit: function(temp){
			this.edit.jid=temp.jid;
			this.edit.pid=temp.pid;
			this.edit.jname=temp.jname;
			this.edit.jname2=temp.jname;
			this.edit.pname=this.department[temp.pid].pname;
		},
		sendData: function(){
			switch(this.op){
				case 'add':
					this.sendAdd();
					break;
				case 'edit':
					this.sendEdit();
					break;
				case 'del':
					this.sendDrop();
					break;
				default:
			}
		},
		//添加添加数据
		sendAdd: function(){
			if (REG.jname.check(this.edit.jname2)==false){
				this.setChk(0,'warning','职务名称为1~20个字符组成，不能包含特殊字符','jname');
				return;
			}
			ajax.data={
				pid: this.edit.pid,
				jname: this.edit.jname2
			};
			ajax.url=URL.jobadd;
			ajax.send();
		},
		//提交修改数据
		sendEdit: function(){
			if (REG.jname.check(this.edit.jname2)==false){
				this.setChk(0,'warning','职务名称为1~20个字符组成，不能包含特殊字符','jname');
				return;
			}
			if (this.edit.jname2==this.edit.jname && this.edit.pid==this.list[this.edit.jid].pid){
				this.setChk(0,'warning','当前职务信息未做任何修改，无需提交','all');
				return;
			}
			ajax.data={
				jid: this.edit.jid,
				pid: this.edit.pid,
				jname: this.edit.jname2
			};
			ajax.url=URL.jobedit;
			ajax.send();
		},
		//提交删除数据
		sendDrop: function(){
			dialog.close('opsure');
			ajax.data={
				jid: this.edit.jid
			};
			ajax.url=URL.jobdrop;
			ajax.send();
		}
	},
	created:function(){
		this.chk=this._getChkobj(1);
		this.list=JSON.array2Object(JSON.parse($('#job').text()),'jid');
		this.department=JSON.array2Object(JSON.parse($('#department').text()),'pid');
		for (var x in this.list){
			var temp=this.list[x].pid;
			if (!this.department[temp].list) this.department[temp].list=[];
			this.department[temp].list.push(this.list[x]);
		}
	},
	watch:{
		'edit.jname2':function(newVal){
			if (this.chk[0].obj=='jname' || this.chk[0].obj=='all') this.clearChk(0);
		},
		'edit.pid':function(newVal){
			if (this.chk[0].obj=='all') this.clearChk(0);
		}
	}
});

var dialog=relaxDialog();
var ajax=new relaxAJAX();
ajax.before=function(){
	vu.load=true;
	if (vu.op=='del'){
		vu.setChk(0,'loading','正在尝试删除职务信息，请稍等...');
	}else{
		vu.setChk(0,'loading','正在提交数据，请稍等...');
	}
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
			msg='职务已经添加成功！';
			var temp={jid:data.data,jname:vu.edit.jname2,pid:vu.edit.pid};
			vu.list[data.data]=temp;
			if (!vu.department[vu.edit.pid].list) vu.department[vu.edit.pid].list=[];
			vu.department[vu.edit.pid].list.push(temp);
		}else if (vu.op=='edit'){
			msg='职务资料已经修改成功！';
			vu.list[vu.edit.jid].jname=vu.edit.jname2;
			if (vu.edit.pid!=vu.list[vu.edit.jid].pid){
				var lastIndex=vu.list[vu.edit.jid].pid;
				vu.list[vu.edit.jid].pid=vu.edit.pid;
				vu.department[vu.edit.pid].list.push(vu.list[vu.edit.jid]);
				var index;
				for (var i=0; i<vu.department[lastIndex].list.length; i++){
					if (vu.department[lastIndex].list[i].jid==vu.edit.jid){
						index=i;
						break;
					}
				}
				if (index!==undefined){
					vu.department[lastIndex].list.splice(index,1);
				}
			}
		}else{
			msg='职务 '+vu.edit.jname+' 已经成功删除！';
			delete vu.list[vu.edit.jid];
			var index;
			for (var i=0; i<vu.department[vu.edit.pid].list.length; i++){
				if (vu.department[vu.edit.pid].list[i].jid==vu.edit.jid){
					index=i;
					break;
				}
			}
			if (index!==undefined){
				vu.department[vu.edit.pid].list.splice(index,1);
			}
		}
		vu.setChk(0,'ok',msg);
		setTimeout(function(){
			vu.clearChk(0);
			vu.load=false;
			vu.hideDialog('jobop');
		},2000);
	}
};

