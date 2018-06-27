<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>请示列表</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
<style type="text/css">
.nowStatus{ padding:3px 5px; border-radius:6px; font-weight:normal; border:1px solid #ccc;}
.nowStatus.st0{ background-color:#fff3dc; border-color:#f90;}
.nowStatus.st0:before{ content:'待接受'; color:#f90; font-weight:600;}
.nowStatus.st1{ background-color:#dae7ff; border-color:#3362b8;}
.nowStatus.st1:before{ content:'处理中'; color:#3362b8;}
.nowStatus.st2{ background-color:#1ba50c; border-color:#1ba50c;}
.nowStatus.st2:before{ content:'已完成'; color:#fff;}

#viewOP .dialog-content,#inforOP .dialog-content{ background-color:#eee;}
.datafill{ background-color:#fff; padding-bottom:10px; box-shadow:0 2px 5px rgba(0,0,0,.2);}
.buttonBar{ text-align:center; padding:10px;}
.buttonBar .rexButton{ width:100px; margin:0 5px;}

.lay2col.style1>*{ min-width:200px;}
.txt.fa-clock-o{ color:red;}
</style>
<body>
	<div id="app" class="outFrame rexFrame nb nl header">
		<div class="rexTopbar">
			<h2 class="bkStyle2"><span class="fa fa-paste"></span> 请示列表</h2>
			<span class="tools">
				<span class="count">共查找到 <strong>{{list.length}}</strong> 条请示信息</span>
				<button class="rexButton ss fa fa-plus warning" title="新增请示" v-if="isPublic" @click="showDialog('inforOP')"></button>
				<button class="rexButton ss fa fa-search infor" title="打开查询条件编辑" @click="showDialog('selreal')"></button>
			</span>
		</div>
		
		<div class="rexRightpart">
			<div class="tableFrame">
				<table class="rexTable fixed">
				<tr>
					<th width="40"></th>
					<th width="80">状态</th>
					<th width="25%">请示标题</th>
					<th>发布时间</th>
					<th width="9%">发布人</th>
					<th width="60">附件</th>
					<th width="60">回复</th>
					<th width="80">操作</th>
				</tr>
				<tr v-for="(item,index) in list">
					<td>{{(real.nowpage-1)*CFG.pagesize+index+1}}</td>
					<td><strong class="nowStatus" v-bind:class="'st'+item.status"></strong></td>
					<td><span class="txt">{{item.title}}</span></td>
					<td>{{item.datemake}}</td>
					<td>{{item.author}}</td>
					<td>{{item.annex.length}}</td>
					<td>{{item.rcount}}</td>
					<td>
						<a class="rexButton ss fa fa-eye infor" @click="showDialog('viewOP',item.cid)"> 查看</a>
					</td>
				</tr>
			</table>
			</div>
		</div>
		
		<!-- 删除/撤销确认 -->
		<div id="sure" class="extDialog">
			<div class="dialogFrame">
				<div class="dialog-title warning">
					<h4 class="t3"><span class="fa fa-search"></span>&emsp;<span class="diy">系统信息提示</span></h4>
				</div>
				<div class="dialog-buttonBar">
					<div class="bntInside">
						<button class="rexButton opBnt" @click="hideDialog('sure')"> 取 消</button>
						<button class="rexButton opBnt infor"> 确 定</button>
					</div>
				</div>
				<div class="dialog-content">
					<div class="diy">是否确认删除/撤销任务 <strong>{{viewobj? viewobj.title:""}} </strong>？</div>
				</div>
			</div>
		</div>
		
		<!-- 查询条件编辑 -->
		<div id="selreal" class="extDialog">
			<div class="dialogFrame">
				<div class="dialog-title">
					<span class="opBnt right fa fa-lg fa-times" @click="hideDialog('selreal')"></span>
					<h4 class="t3"><span class="fa fa-search"></span>&emsp;<span class="diy">编辑查询条件</span></h4>
				</div>
				<div class="dialog-buttonBar">
					<div class="bntInside">
						<button class="rexButton opBnt" @click="clearRealation()"> 清 除</button>
						<button class="rexButton opBnt infor" @click="sendData()"> 查 询</button>
					</div>
				</div>
				<div class="dialog-content">
					<ul class="lay2col style1">
						<li class="formpart">
							<label class="rexLabel">标&emsp;题</label><span>
								<input type="text" class="rexInput" v-mode="real2.key"/>
							</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">状&emsp;态</label><span>
								<select class="rexSelect" v-model="real2.status">
									<option value=""></option>
									<option value="0">待接受</option>
									<option value="1">处理中</option>
									<option value="3">已完成</option>
								</select>
							</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<!-- 回复弹框 -->
		<div id="answer" class="extDialog">
			<div class="dialogFrame">
				<div class="dialog-title">
					<span class="opBnt right fa fa-lg fa-times" @click="hideDialog('answer')"></span>
					<h4 class="t3"><span class="fa fa-comment-o"></span>&emsp;<span class="diy">任务进度回复</span></h4>
				</div>
				<div class="dialog-buttonBar">
					<div class="bntInside">
						<button class="rexButton opBnt infor"> 提交回复</button>
					</div>
				</div>
				<div class="dialog-content">
					<ul class="lay2col style1">
						<li class="formpart alone">
							<label class="rexLabel">完成设置</label><span>
								<span class="rexCheck">
									<input type="radio" name="returntype" value="1" checked="checked"/>
									<label>请示完成</label>
								</span>
								<span class="rexCheck">
									<input type="radio" name="returntype" value="0"/>
									<label>还待继续跟进</label>
								</span>
							</span>
						</li>
						<li class="formpart alone">
							<label class="rexLabel">回复内容</label><span class="request">
								<textarea class="rexTxtarea"></textarea>
							</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<!-- 查看详情弹出页面 -->
		<div id="viewOP" class="extPage">
			<div class="dialogFrame">
				<div class="dialog-title">
					<span class="opBnt right fa fa-lg fa-times" @click="hideDialog('viewOP')"></span>
					<span class="opBnt left fa fa-lg fa-pencil" title="修改当前请示信息" v-if="viewobj.authorid==me.uid" @click="showDialog('inforOP')"></span>
					<h4 class="t3"><span class="fa fa-eye"></span>&emsp;<span class="diy">查看请示详情</span></h4>
				</div>
				<div class="dialog-content">
					<div class="datafill">
						<ul class="lay2col">
							<li class="formpart view">
								<label class="rexLabel">请示状态</label><span>
									<span v-bind:class="'nowStatus'+viewobj.status"></span>
								</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">请示标题</label><span>{{viewobj.title}}</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">发&ensp;布&ensp;人</label><span>{{viewobj.author}}</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">发布日期</label><span>{{viewobj.datemake}}</span>
							</li>
							<li class="alone formpart view">
								<label class="rexLabel">请示内容</label><span>{{viewobj.content}}</span>
							</li>
							<li class="alone formpart table">
								<table class="rexRowtable notitle fixed">
									<caption class="captionTitle">
										<span class="fa fa-chain"> 附件列表</span>
									</caption>
									<tr v-for="(item,index) in viewobj.annex">
										<td width="35">{{index+1}}</td>
										<td><a v-bind:href="getFileURL(item)" target="_blank">{{item.name}}</a></td>
										<td width="10">
										</td>
									</tr>
									<tr class="alone" v-if="viewobj && viewobj.annex.length==0">
										<td colspan="3" class="tipMessage warning tCT"><span class="fa fa-lg fa-warning"></span> 暂无任何列表信息</td>
									</tr>
								</table>
							</li>
							<li class="alone formpart table returnList">
								<div class="captionTitle">
									<span class="fa fa-comments-o"> 回复列表</span>
								</div>
								<div v-for="item in returnList[viewobj.cid]" v-bind:class="item.classstr">
									<span class="reuser"><strong>{{item.username}}</strong>{{item.datemake}}</span>
									<div class="recontent">{{item.content}}</div>
								</div>
							</li>
						</ul>
					</div>
					<div class="buttonBar" v-if="me.tid!=CFG.UM">
						<button class="rexButton infor" v-if="bs4">接受请示</button>
						<button class="rexButton infor" @click="showDialog('answer')" v-if="bs1">回复请示</button>
						<button class="rexButton alert" @click="showDialog('sure')" v-if="bs3">删除请示</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- 添加/编辑任务页面-->
		<div id="inforOP" class="extPage">
			<div class="dialogFrame">
				<div class="dialog-title">
					<h4 class="t3"><span class="fa" v-bind:class="{'fa-plus':op=='add','fa-pencil':op=='edit'}"></span> <span class="diy">{{op=='add'?'新增请示信息':'编辑请示信息'}}</span></h4>
				</div>
				<div class="dialog-content">
					<div class="datafill">
						<ul class="lay2col">
							<li class="formpart">
								<label class="rexLabel">请示标题</label><span class="request">
									<input type="text" class="rexInput" v-model.trim="edit.title" autocomplete="off" maxlength="50"/>
								</span>
							</li>
							<li class="alone formpart">
								<label class="rexLabel">请示内容</label><span class="request">
									<textarea class="rexTxtarea" v-model.trim="edit.content"></textarea>
								</span>
							</li>
							<li class="alone formpart table">
								<table class="rexRowtable notitle fixed">
									<caption class="captionTitle">
										<span class="fa fa-chain"> 附件列表</span>
										<button class="rexButton fa fa-plus ss infor" title="添加附件" @click="showDialog('filelist')"></button>
									</caption>
									<tr v-for="(item,index) in edit.annex">
										<td width="10%">{{index+1}}</td>
										<td><a v-bind:href="getFileURL(item)" target="_blank">{{item.name}}</a></td>
										<td width="20%">
											<button class="rexButton ss fa fa-pencil" @click="annexOP('edit',index)"></button>
											<button class="rexButton ss fa fa-trash-o" @click="annexOP('del',index)"></button>
										</td>
									</tr>
									<tr class="alone" v-if="edit.annex.length==0">
										<td colspan="3" class="tipMessage"><span class="fa warning"></span> 暂无任何列表信息</td>
									</tr>
								</table>
							</li>
						</ul>
					</div>
					<div class="buttonBar">
						<button class="rexButton" @click="hideDialog('inforOP')">取 消</button>
						<button class="rexButton infor">提 交</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- 附件编辑弹框 -->
		<div id="filelist" class="extDialog" noclick="noclick">
			<div class="dialogFrame">
				<div class="dialog-title">
					<h4 class="t3"><span class="fa fa-chain"></span> <span class="diy">附件编辑</span></h4>
				</div>
				<div class="dialog-buttonBar">
					<div class="bntInside">
						<button class="rexButton opBnt" @click="hideDialog('filelist')"> 取 消</button>
						<button class="rexButton opBnt infor" @click="annexOP('add')"> 确 定</button>
					</div>
				</div>
				<div class="dialog-content sP1">
					<div class="fillForm2">
						<div class="formpart alone">
							<label class="rexLabel">地址类型</label><span>
								<span class="rexCheck">
									<input type="radio" name="urltype" value="1" v-model="axTemp.type"/>
									<label>默认FTP</label>
								</span>
								<span class="rexCheck">
									<input type="radio" name="urltype" value="0" v-model="axTemp.type"/>
									<label>自定义URL</label>
								</span>
							</span>
						</div>
						<div class="formpart alone">
							<label class="rexLabel">文件路径</label><span class="request filepath">
								<span v-if="axTemp.type=='1'">{{ftp}}</span>
								<input type="text" class="rexInput" v-bind:class="{'warning':chk[2].obj=='url'}" v-model="axTemp.url"/>
							</span>
						</div>
						<div class="formpart alone">
							<label class="rexLabel">附件名称</label><span>
								<input type="text" class="rexInput" v-model="axTemp.name"/>
							</span>
						</div>
						<div class="tipMessage" v-bind:class="chk[2].flag" v-if="chk[2].flag">
							<span class="fa fa-lg" v-bind:class="{'fa-warning':chk[2].flag=='warning'}">&ensp;{{chk[2].msg}}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
					
	</div>
	
	<div id="FTP" class="dataField"><?php echo ''; ?></div>
	<div id="user" class="dataField"><?php echo json_encode($user); ?></div>
	<div id="department" class="dataField"><?php echo json_encode($department); ?></div>
	<div id="account" class="dataField"><?php echo json_encode($account); ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/dialog1.3.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/md5.min.js"></script>
	<script src="/script2/config.js"></script>
<script>
var vu=new Vue({
	el: "#app",
	data:{
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

var dialog=relaxDialog();
</script>
</body>
</html>