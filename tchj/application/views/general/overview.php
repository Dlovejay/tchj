<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>太仓海警支队--概览页面</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
<style type="text/css">
.outFrame{ background-color:#fff;}
.rexTab{ background-color:#eee;}
.overview{ width:70%; min-width:340px; max-width:500px; margin:10px auto 40px auto;}
.inside{ background-color:#eee; border-radius:6px; margin-top:10px; padding-bottom:15px; padding-top:10px; position:relative; top:-15px; border:1px solid #ccc;}
h3{ color:#7278ad; position:relative; z-index:10;}
h3 .fa{ display:inline-block; width:40px; height:40px; border-radius:50%; background-color:#7278ad; vertical-align:middle; color:#fff; text-align:center; line-height:40px; overflow:hidden; font-size:24px; margin-right:10px;}
.style h3{ color:#5DA159;}
.style h3 .fa{ background-color:#5DA159;}
.part{ padding-left:50px;}
.part>span{ margin-right:20px;}
.part strong{ font-size:1.1em; color:#fff; position:relative; margin-left:5px;}
.part strong:before{ content:''; position:absolute; width:0px; height:0px; overflow:hidden; font-size:0; left:-11px; top:5px;}
.show1{ margin-bottom:20px;}
.show1 span{ margin-right:30px; font-size:16px; color:#333;}
.show1 strong{ background-color:transparent; font-size:1.5em; color:#f90;}
.show1 strong:before{ display:none;}
.all{ color:#4B63D5;}
.all strong{ background-color:#4B63D5;}
.all strong:before{  border:5px solid transparent; border-right-color:#4B63D5;}
.now{ color:#00A300;} 
.now strong{ background-color:#00A300;}
.now strong:before{  border:5px solid transparent; border-right-color:#00A300;}
.remove{ color:#999;}
.remove strong{ background-color:#999;}
.remove strong:before{  border:5px solid transparent; border-right-color:#999;}
.timeout{ color:#FF1724;}
.timeout strong{ background-color:#FF1724;}
.timeout strong:before{  border:5px solid transparent; border-right-color:#FF1724;}
</style>
</head>
<body>
	<div id="app" class="outFrame rexFrame nb nl header">
		<div class="rexTopbar tCT">
			<h2 class="bkStyle1"><span class="fa fa-bar-chart-o"></span> 工作概览</h2>
			<span class="tools">
				<button class="rexButton ss fa fa-lg fa-refresh warning" v-if="" @click="getAll()" title="刷新统计数据"></button>
			</span>
		</div>
		<div class="rexRightpart">
			<ul class="rexTab tCT" v-if="datalist.length>1">
				<li class="rexItem" v-for="(item,index) in datalist" v-bind:class="{'sel':viewindex==index}" @click="changeView(index)">{{item.pname}}</li>
			</ul>
			<div v-for="(item,index) in datalist" v-if="viewindex==index">
				<div class="overview">
					<h3><span class="fa fa-calendar"></span>任务统计</h3>
					<div class="inside" v-if="task['0']">
						<div class="part show1">
							<span title="首次完成率=无退回完成的任务数/完成的任务总数"> 首次完成率 <strong>{{task[item.pid].first}}</strong></span>
							<span title="提交率=已提交待审核的任务数/进行中的任务总数"> 提交率 <strong>{{task[item.pid].reply}}</strong></span>
						</div>
						<div class="part">
							<span class="all">任务总数 <strong class="rexTip">{{task[item.pid].total}}</strong></span>
							<span class="now">进行中 <strong class="rexTip">{{task[item.pid].doing}}</strong></span>
							<span class="timeout">超时 <strong class="rexTip">{{task[item.pid].timeout}}</strong></span>
							<span class="remove">已撤销 <strong class="rexTip">{{task[item.pid].repeal}}</strong></span>
						</div>
					</div>
				</div>
				<div class="overview style">
					<h3><span class="fa fa-paste"></span>请示统计</h3>
					<div class="inside"  v-if="consult['0']">
						<div class="part">
							<span class="all">请示总数 <strong class="rexTip all">{{consult[item.pid].total}}</strong></span>
							<span class="now">进行中 <strong class="rexTip now">{{consult[item.pid].doing}}</strong></span>
							<span class="remove">已撤销 <strong class="rexTip remove">{{consult[item.pid].repeal}}</strong></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div id="loading" class="extDialog" noclick="noclick">
			<div class="dialogFrame">
				<div class="dialog-title">
					<h4 class="t3"><span class="fa fa-exclamation-circle"></span>&emsp;<span class="diy">加载中提示</span></h4>
				</div>
				<div class="dialog-content">
					<div class="tipMessage sP1">
						<span class="fa" v-bind:class="chk.flag"> {{chk.msg}}</span>
					</div>
				</div>
			</div>
		</div>
		
		<div id="showinfor" class="extDialog" noclick="noclick">
			<div class="dialogFrame">
				<div class="dialog-title warning">
					<h4 class="t3"><span class="fa fa-exclamation-circle"></span>&emsp;<span class="diy">系统信息提示</span></h4>
				</div>
				<div class="dialog-buttonBar">
					<div class="bntInside">
						<button class="rexButton" @click="hideDialog('showinfor')"> 取 消</button>
						<button class="rexButton infor" @click="getTask()" v-bind:disabled="load.re"> 重 试</button>
					</div>
				</div>
				<div class="dialog-content">
					<div class="tipMessage">
						<span class="fa" v-bind:class="chk.flag"> {{chk.msg}}</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/dialog1.3.1.js"></script>
	<script src="/script/config.js"></script>
<script>
var vu=new Vue({
	el: "#app",
	data:{
		task:{},
		consult:{},
		load:false,
		ajaxtype:'',
		error:{},
		chk:{
			flag:'',
			msg:''
		},
		viewindex:0
	},
	computed:{
		datalist: function(){
			var temp=[];
			if (this.task['0']){
				for (var x in this.task){
					temp.push({pid:x,pname:this.task[x].pname});
				}
			}else if (this.consult['0']){
				for (var x in this.consult){
					temp.push({pid:x,pname:this.consult[x].pname});
				}
			}
			return temp;
		}
	},
	methods:{
		showDialog: function(txt){
			dialog.open(txt);
		},
		hideDialog: function(txt){
			if (txt=='loading'){
				this.chk.flag='';
				this.chk.msg='';
			}
			dialog.close(txt);
		},
		AJAXbefore: function(){
			this.load=true;
			this.chk.flag='loading';
			if (this.ajaxtype=='task'){
				this.chk.msg='正在加载任务统计信息，请稍后...';
			}else{
				this.chk.msg='正在加载请示统计信息，请稍后...';
			}
			this.showDialog('loading');
		},
		AJAXerror: function(code,msg){
			var tempMsg='';
			this.error[this.ajaxtype]=code+' '+msg;
			if (this.ajaxtype=='task'){
				if (this.error['consult']){
					tempMsg=this.error['consult']+'\n'+code+' '+msg;
				}else{
					if (!this.consult['0']){
						this.getConsult();
						return;
					}
				}
			}else{
				if (this.error['task']){
					tempMsg=this.error['task']+'\n'+code+' '+msg;
				}else{
					if (!this.task['0']){
						this.getTask();
						return;
					}
				}
			}
			this.hideDialog('loading');
			this.load=false;
			this.chk.flag='alert';
			this.chk.msg=tempMsg;
			this.showDialog('showinfor');
		},
		AJAXsuccess: function(data){
			if (this.ajaxtype=='task'){
				this.task=this._makeTask(data.data);
				if (!this.error['consult'] && !this.consult['0']){
					this.getConsult();
					return;
				}
			}else{
				this.consult=this._makeConsult(data.data);
				if (!this.error['task'] && !this.task['0']){
					this.getTask();
					return;
				}
			}
			this.load=false;
			this.ajaxtype='';
			this.hideDialog('loading');
		},
		getData: function(){
			if (!this.consult['0']){
				this.getConsult();
			}else if(!this.task['0']){
				this.getTask();
			}
		},
		getAll: function(){
			this.task={};
			this.consult={};
			this.error={};
			this.getData();
		},
		getTask: function(){
			this.ajaxtype='task';
			ajax.url=URL.taskover;
			ajax.send();
		},
		getConsult: function(){
			this.ajaxtype='consult';
			ajax.url=URL.consultover;
			ajax.send();
		},
		changeView: function(index){
			if (this.viewindex==index) return;
			this.viewindex=index;
		},
		_makeTask: function(data){
			var first=data[0].count;
			var temp={};
			temp['0']={
				pid: 0,
				pname: '总计',
				total: first.total,
				doing: first.doing,
				repeal: first.repeal,
				timeout: first.timeout,
				first: first.total-first.doing==0? '--':first.first_finish_percent+'%',
				reply: first.doing==0? '--':first.reply_percent+'%'
			};
			for (var i=1; i<data.length; i++){
				var temp2=data[i];
				alert(temp2.count.total);
				temp[temp2.pid]={
					pid: temp2.pid,
					pname: temp2.name,
					total: temp2.count.total,
					doing: temp2.count.doing,
					repeal: temp2.count.repeal,
					timeout: temp2.count.timeout,
					first: temp2.count.total-temp2.count.doing==0? '--':temp2.count.first_finish_percent+'%',
					reply: temp2.count.doing==0? '--':temp2.count.reply_percent+'%'
				};
			}
			return temp;
		},
		_makeConsult: function(data){
			var temp={};
			temp['0']={
				pid:0,
				pname:'总计',
				total: data.total,
				doing: data.total_ongoing,
				repeal: data.total_revoke
			}
			if (data.departments.length>1){
				for (var i=0; i<data.departments.length; i++){
					var temp2=data.departments[i];
					temp[temp2.pid]={
						pid: temp2.pid,
						pname: temp2.pname,
						total: temp2.total_cnt,
						doing: temp2.ongoing_cnt,
						repeal: temp2.revoke_cnt
					};
				}
			}
			return temp;
		}
	}
});
var dialog=relaxDialog();
var ajax=new relaxAJAX();
ajax.before=vu.AJAXbefore;
ajax.error=vu.AJAXerror;
ajax.success=vu.AJAXsuccess;
vu.getData();
</script>
</body>
</html>