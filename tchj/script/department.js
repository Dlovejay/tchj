var vu=new Vue({
  el:'#app',
  data:{
		list:{},  //pid索引的对象
		level:{}, //plevel索引的pid数组
		levelName:{
			'0':'支队名称',
			'1':'上级部门',
			'2':'下级部门'
		},
		edit:{
			pid:'',
			pname:'',
			pname2:'',
			plevel:''
		},
		op:'',
		chk:'',
		load:false
  },
	computed:{
		getTypeName:function(){
			switch(this.edit.plevel){
				case '0':
					return '支队';
				case '1':
					return '上级部门';
				case '2':
					return '下级部门';
				default:
					return '';
			}
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
		showDialog:function(optype,keynum){
			if (optype=='edit'){
				this.op='edit';
				this.editInit(this.list[keynum]);
				dialog.open('departmentop');
			}else if(optype=='add'){
				this.op='add';
				this.edit.plevel=keynum;
				dialog.open('departmentop');
			}else{
				this.op='del';
				dialog.open('opsure',{content:'请确认是否删除部门 <strong>'+ this.edit.pname+'</strong> ?'});
			}
		},
		hideDialog:function(name){
			if (name=='departmentop'){
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
			this.edit.pid=temp.pid;
			this.edit.pname=temp.pname;
			this.edit.pname2=temp.pname;
			this.edit.plevel=temp.plevel;
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
			if (REG.pname.check(this.edit.pname2)==false){
				this.setChk(0,'warning','部门名称为1~20个字符组成，不能包含特殊字符','pname');
				return;
			}
			ajax.data={
				pname: this.edit.pname2,
				plevel: this.edit.plevel
			};
			ajax.url=URL.departmentadd;
			ajax.send();
		},
		//提交修改数据
		sendEdit: function(){
			if (REG.pname.check(this.edit.pname2)==false){
				this.setChk(0,'warning','部门名称为1~20个字符组成，不能包含特殊字符','pname');
				return;
			}
			if (this.edit.pname2==this.edit.pname){
				this.setChk(0,'warning','当前部门名称未做任何修改，无需提交','pname');
				return;
			}
			ajax.data={
				pid: this.edit.pid,
				pname: this.edit.pname2
			};
			ajax.url=URL.departmentedit;
			ajax.send();
		},
		//提交删除数据
		sendDrop: function(){
			dialog.close('opsure');
			ajax.data={
				pid: this.edit.pid
			};
			ajax.url=URL.departmentdrop;
			ajax.send();
		}
	},
	created:function(){
		this.chk=this._getChkobj(1);
		var temp=JSON.parse($('#department').text());
		this.list=JSON.array2Object(temp,'pid');
		var key;
		for (var i=0; i<temp.length; i++){
			key=temp[i].plevel;
			if (!this.level[key]) this.level[key]=[];
			this.level[key].push(this.list[temp[i].pid]);
		}
	},
	watch:{
		'edit.pname2':function(newVal){
			if (this.chk[0].obj=='pname') this.clearChk(0);
		}
	}
});

var dialog=relaxDialog();
var ajax=new relaxAJAX();
ajax.before=function(){
	vu.load=true;
	if (vu.op=='del'){
		vu.setChk(0,'loading','正在尝试删除部门信息，请稍等...');
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
			msg='部门已经添加成功！';
			var temp={pid:data.data,pname:vu.edit.pname2,plevel:vu.edit.plevel};
			vu.list[data.data]=temp;
			vu.level[vu.edit.plevel].push(temp);
		}else if (vu.op=='edit'){
			msg='部门资料已经修改成功！';
			vu.list[vu.edit.pid].pname=vu.edit.pname2;
		}else{
			msg='部门 '+vu.edit.pname+' 已经成功删除！';
			delete vu.list[vu.edit.pid];
			var index;
			for (var i=0; i<vu.level[vu.edit.plevel].length; i++){
				if (vu.level[vu.edit.plevel][i].pid==vu.edit.pid){
					index=i;
					break;
				}
			}
			if (index!==undefined){
				vu.level[vu.edit.plevel].splice(index,1);
			}
		}
		vu.setChk(0,'ok',msg);
		setTimeout(function(){
			vu.clearChk(0);
			vu.load=false;
			vu.hideDialog('departmentop');
		},2000);
	}
};

