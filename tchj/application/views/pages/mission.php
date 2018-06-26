<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>任务列表</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
	<link rel="stylesheet" href="/style/mission.css?v=1"/>
<body>
	<div id="app" class="outFrame">
		<div class="pageHeader leftshow">
			<h2 class="fa fa-calendar"> 任务管理</h2>
			<span class="rexLabel t1">共找到 <strong>{{real.total}}</strong> 条任务信息&ensp;</span>
			<button class="rexButton fa ss fa-plus warning" title="新增任务" v-if="isPublic" @click="showDialog('inforOP')"></button>
		</div>

		<div class="listshow">
			<div class="inside">
				<table class="rexTable style1 fixed">
					<tr>
						<th width="40"></th>
						<th width="80">状态</th>
						<th width="25%">任务标题</th>
						<th>任务时间</th>
						<th width="9%">发布人</th>
						<th>指派给</th>
						<th width="60">附件</th>
						<th width="60">回复</th>
						<th width="80">操作</th>
					</tr>
					<tr v-for="(item,index) in list">
						<td>{{(real.nowpage-1)*CFG.cut+index+1}}</td>
						<td><strong class="nowStatus" v-bind:class="'st'+item.status"></strong></td>
						<td><span class="txt"><strong class="tips" v-if="item.tips">[{{item.tips}}]&ensp;</strong>{{item.mtitle}}</span></td>
						<td><span class="txt t1 fa fa-lg" v-bind:class="{'fa-clock-o':item.timeout=='1'}"> {{item.date}}</span></td>
						<td>{{item.author}}</td>
						<td><span class="txt">{{item.pass}}</span></td>
						<td>{{item.annex}}</td>
						<td>{{item.rcount}}</td>
						<td>
							<a class="rexButton ss fa fa-eye infor" @click="showDialog('viewOP',item.mid)"> 查看</a>
						</td>
					</tr>
					<tr class="alone" v-if="chk[0].flag!=''">
						<td colspan="9" class="tipMessage" v-bind:class="chk[0].flag">
							<span class="fa fa-lg" v-bind:class="{'fa-warning':chk[0].flag=='warning','fa-times-circle':chk[0].flag=='alert'}">&ensp;{{chk[0].msg}}</span>
						</td>
					</tr>
				</table>
				<!-- 分页-->
				<ul class="cutpage noHead t1" v-if="real.pages>0">
					<li class="rexButton ss" v-for="n in real.pages" v-bind:class="{'infor':n!=real.nowpage,'warning':n==real.nowpage}" v-bind:disabled="load.list" @click="gopage(n)">{{n}}</li>
				</ul>
			</div>
		</div>
		
		<!-- 查询条件-->
		<div class="selectRelation t1">
			<label class="rexLabel">状态</label>
			<select class="rexSelect" v-model="real2.status">
				<option value=""></option>
				<option value="0">未发布</option>
				<option value="1">待接受</option>
				<option value="2">处理中</option>
				<option value="3">待评审</option>
				<option value="4">已完成</option>
				<option value="5">退回</option>
				<option value="6">撤销</option>
			</select>
			<label class="rexLabel">&emsp;超时</label>
			<select class="rexSelect" v-model="real2.timeout">
				<option value=""></option>
				<option value="1">是</option>
				<option value="0">否</option>
			</select>
			<label class="rexLabel">&emsp;关键字</label>
			<input type="text" class="rexInput" v-model="real2.key"/>
			<label class="rexLabel">&emsp;开始日期</label>
			<input type="date" class="rexInput" v-model="real2.datestart"/>
			<label class="rexLabel">&emsp;结束日期</label>
			<input type="date" class="rexInput" v-model="real2.dateend"/>
			<button class="rexButton" @click="clearRealation()" v-bind:disabled="load.list">清除</button>
			<button class="rexButton" @click="getListData()" v-bind:disabled="load.list">查询</button>
		</div>
		
		<!-- 提示信息弹框 -->
		<div id="msgshow" class="extDialog" noclick="noclick">
			<div class="dialogFrame">
				<div class="dialog-title">
					<h4 class="t3"><span class="diy">信息提示</span></h4>
				</div>
				<div class="dialog-buttonBar">
					<div class="bntInside">
						<button class="rexButton opBnt bnt-close"> 确 定</button>
					</div>
				</div>
				<div class="dialog-content sP1">
					<div class="diy tCT"></div>
				</div>
			</div>
		</div>
		
		<!-- 查看详情弹出页面 -->
		<div id="viewOP" class="extPage">
			<div class="dialogFrame">
				<div class="dialog-title">
					<span class="opBnt right fa fa-lg fa-times" v-if="load.re==false" @click="hideDialog('viewOP')"></span>
					<span class="opBnt left fa fa-lg fa-pencil" title="修改当前任务信息" v-if="viewobj.authorid==me.uid && load.re==false" @click="showDialog('inforOP')"></span>
					<h4 class="t3"><span class="fa fa-calendar"></span>&emsp;<span class="diy">查看任务详情</span></h4>
				</div>
				<div class="dialog-content">
					<div class="fillForm showSplit">
						<ul class="rexLayout2">
							<li class="itempart alone">
								<label class="rexLabel">任务状态</label><span>
									<strong class="nowStatus" v-bind:class="'st'+viewobj.status"></strong>
									<strong class="timeoutStatus" v-if="viewobj.timeout=='1'">任务超时</strong>
								</span>
							</li>
							<li class="itempart">
								<label class="rexLabel">任务标题</label><span>{{viewobj.mtitle}}</span>
							</li>
							<li class="itempart">
								<label class="rexLabel">发布部门</label><span>{{viewobj.tips?viewobj.tips:'--'}}</span>
							</li>
							<li class="itempart">
								<label class="rexLabel">开始日期</label><span>{{viewobj.datestart}}</span>
							</li>
							<li class="itempart">
								<label class="rexLabel">截止日期</label><span>{{viewobj.dateend?viewobj.dateend:'--'}}</span>
							</li>
							<li class="alone itempart">
								<label class="rexLabel">任务说明</label><span>{{viewobj.content}}</span>
							</li>
							<li class="alone itempart">
								<label class="rexLabel">指派部门</label><span>{{viewobj.pass}}</span>
							</li>
							<li class="alone itempart table">
								<table class="rexTable style2 rows notitle fixed">
									<caption class="captionTitle t1">
										<span class="fa fa-chain"> 附件列表</span>
									</caption>
									<tr v-for="(item,index) in viewobj.annexobj">
										<td width="35">{{index+1}}</td>
										<td><a v-bind:href="getFileURL(item)" target="_blank">{{item.name}}</a></td>
										<td width="10">
										</td>
									</tr>
									<tr class="alone" v-if="viewobj && viewobj.annexobj.length==0">
										<td colspan="3" class="tipMessage warning tCT"><span class="fa fa-lg fa-warning"></span> 暂无任何列表信息</td>
									</tr>
								</table>
							</li>
							<li class="alone itempart table">
								<table class="rexTable style2 rows notitle fixed">
									<caption class="captionTitle t1">
										<span class="fa fa-comments"> 回复列表</span>
										<button class="rexButton fa fa-refresh ss infor" v-bind:disabled="load.re" title="刷新回复列表" @click="getReturnlist()"></button>
									</caption>
									<tr v-if="chk[3].flag=='' && returnList[viewobj.mid] && returnList[viewobj.mid].length==0">
										<td class="tipMessage warning tCT"><span class="fa fa-lg fa-warning"></span> 暂无任何回复信息</td>
									</tr>
									<tr v-if="chk[3].flag">
										<td class="tipMessage tCT" v-bind:class="{'fa-warning':chk[3].flag=='warning','fa-times-circle':chk[3].flag=='alert'}">
											<span class="fa" v-bind:class="{'fa-warning':chk[3].flag=='warning','fa-times-circle':chk[3].flag=='alert'}"> {{chk[3].msg}}</span>
										</td>
									</tr>
								</table>
							</li>
							<li class="alone table itempart returnList">
								<div v-for="item in returnList[viewobj.mid]" v-bind:class="item.classstr">
									<span class="reuser"><strong>{{item.username}}</strong>{{item.datemake}}</span>
									<div class="recontent">{{item.content}}</div>
								</div>
							</li>
							<li class="alone itempart" v-if="viewobj.status>0 && viewobj.status!=6">
								<label class="rexLabel">我要回复</label><span>
									<textarea class="rexTxtarea"></textarea>
								</span>
							</li>
							<li class="alone sP1" v-if="viewobj.status>0">
								<button class="rexButton infor">提交回复</button>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<!-- 添加/编辑任务页面-->
		<div id="inforOP" class="extPage">
			<div class="dialogFrame">
				<div class="dialog-title">
					<h4 class="t3"><span class="fa" v-bind:class="{'fa-plus':op=='add','fa-pencil':op=='edit'}"></span> <span class="diy">{{op=='add'?'新增任务信息':'编辑任务信息'}}</span></h4>
				</div>
				<div class="dialog-content">
					<div class="fillForm">
						<ul class="rexLayout2">
							<li class="itempart">
								<label class="rexLabel">任务标题</label><span class="request">
									<input type="text" class="rexInput" v-bind:class="{'warning':chk[1].obj=='title'}" v-model.trim="edit.title" autocomplete="off" maxlength="50"/>
								</span>
							</li>
							<li class="itempart">
								<label class="rexLabel">发布部门</label><span>
									<input type="text" class="rexInput" v-bind:class="{'warning':chk[1].obj=='tips'}" v-model.trim="edit.tips" autocomplete="off" maxlength="20"/>
								</span>
							</li>
							<li class="itempart">
								<label class="rexLabel">开始日期</label><span class="request">
									<input type="date" v-bind:class="{'warning':chk[1].obj=='datestart'}" class="rexInput" v-model.trim="edit.datestart"/>
								</span>
							</li>
							<li class="itempart">
								<label class="rexLabel">截止日期</label><span title="不填写该项表示当前任务无日期限制">
									<input type="date" v-bind:class="{'warning':chk[1].obj=='dateend'}" class="rexInput" v-model.trim="edit.dateend"/>
								</span>
							</li>
							<li class="alone itempart">
								<label class="rexLabel">任务说明</label><span class="request">
									<textarea class="rexTxtarea" v-bind:class="{'warning':chk[1].obj=='intro'}" v-model.trim="edit.intro"></textarea>
								</span>
							</li>
							<li class="alone itempart">
								<label class="rexLabel">指派部门</label><span>
									<span v-if="isChoose">
										<span class="rexCheck">
											<input type="radio" name="passtype" value="0" v-model="edit.powertype"/>
											<label>全部</label>
										</span>
										<span class="rexCheck">
											<input type="radio" name="passtype" value="1" v-model="edit.powertype"/>
											<label>自定义</label>
										</span><br/>
									</span>
									<span class="rexTurn" v-for="item in getDpmt" v-bind:disabled="edit.powertype==0">
										<input type="checkbox" name="powdpmt" v-bind:value="item.pid" v-model="edit.power"/>
										<label>{{item.pname}}</label>
									</span>
								</span>
							</li>
							<li class="alone itempart table">
								<table class="rexTable style2 rows notitle fixed">
									<caption class="captionTitle t1">
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
										<td colspan="3" class="tipMessage warning tCT"><span class="fa fa-lg fa-warning"></span> 暂无任何列表信息</td>
									</tr>
								</table>
							</li>
						</ul>
					</div>
					<div class="buttonBar">
						<div class="tipMessage" v-if="chk[1].flag" v-bind:class="chk[1].flag">
							<span class="fa fa-lg" v-bind:class="{'fa-warning':chk[1].flag=='warning','fa-times-circle':chk[1].flag=='alert','fa-check-circle':chk[1].flag=='ok'}"> {{chk[1].msg}}</span>
						</div>
						<button class="rexButton" @click="hideDialog('inforOP')" v-bind:disabled="load.op">取 消</button>
						<button class="rexButton infor" @click="sendData()" v-bind:disabled="load.op">提 交</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- 附件编辑弹框 -->
		<div id="filelist" class="extDialog" noclick="noclick">
			<div class="dialogFrame">
				<div class="dialog-title">
					<h4 class="t3"><span class="fa fa-chain"></span> <span class="diy">任务附件编辑</span></h4>
				</div>
				<div class="dialog-buttonBar">
					<div class="bntInside">
						<button class="rexButton opBnt" @click="hideDialog('filelist')"> 取 消</button>
						<button class="rexButton opBnt infor" @click="annexOP('add')"> 确 定</button>
					</div>
				</div>
				<div class="dialog-content sP1">
					<div class="fillForm2">
						<div class="itempart alone">
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
						<div class="itempart alone">
							<label class="rexLabel">文件路径</label><span class="request filepath">
								<span v-if="axTemp.type=='1'">{{ftp}}</span>
								<input type="text" class="rexInput" v-bind:class="{'warning':chk[2].obj=='url'}" v-model="axTemp.url"/>
							</span>
						</div>
						<div class="itempart alone">
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
	
	<div id="URL" class="dataField"><?php echo $config['URLSTR']; ?></div>
	<div id="FTP" class="dataField"><?php echo $config['FTPSTR']; ?></div>
	<div id="USERU" class="dataField"><?php echo $config['USERU']; ?></div>
	<div id="USERL" class="dataField"><?php echo $config['USERL']; ?></div>
	<div id="user" class="dataField"><?php echo json_encode($user); ?></div>
	<div id="department" class="dataField"><?php echo json_encode($department); ?></div>
	<div id="account" class="dataField"><?php echo json_encode($account); ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/dialog1.3.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/md5.min.js"></script>
	<script src="/script/config.js"></script>
<script>
var vu=new Vue({
	el: "#app",
	data:{
		user: JSON.array2Object(JSON.parse($("#user").text()),'uid'),
		department: JSON.array2Object(JSON.parse($("#department").text()),'pid'),
		USERL: $("#USERL").text(),
		USERU: $("#USERU").text(),
		me: JSON.parse($("#account").text()),
		ftp: $('#FTP').text(),
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
		errmsg:'',   //获得列表的出错提示
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
		if (this.me.tid==this.USERL || this.me.tid==this.USERU){
			this.isPublic=true;
			if (this.me.tidv
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

ajax.url=CFGURL+'pages/missionop/add';
ajaxlist.url=CFGURL+'pages/missionop/list';
ajaxre.url=CFGURL+'pages/missionreturn/list';

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
		Vue.set(vu.returnList,vu.viewobj.mid,{
                  parent:this.edit.Model.brand,
                  children:[]
                });
								
								vu.returnList[vu.viewobj.mid]=temp;
	}
};

var dialog=relaxDialog();
vu.getListData(); //自动获得列表信息
</script>
</body>
</html>