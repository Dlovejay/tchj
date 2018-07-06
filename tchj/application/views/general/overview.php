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
.remove{ color:#FF1724;}
.remove strong{ background-color:#FF1724;}
.remove strong:before{  border:5px solid transparent; border-right-color:#FF1724;}
</style>
</head>
<body>
	<div id="app" class="outFrame rexFrame nb nl header">
		<div class="rexTopbar tCT">
			<h2 class="bkStyle1"><span class="fa fa-bar-chart-o"></span> 工作概览</h2>
		</div>
		<div class="rexRightpart">
			<ul class="rexTab tCT">
				<li class="rexItem" v-for="(item,index) in task" v-bind:class="{'sel':viewindex==index}" @click="changeView(index)">{{item.name}}</li>
			</ul>
			<div v-for="(item,index) in task" v-if="viewindex==index">
				<div class="overview">
					<h3><span class="fa fa-calendar"></span>任务统计</h3>
					<div class="inside">
						<div class="part show1">
							<span title="首次完成率=无退回完成的任务数/完成的任务总数"> 首次完成率 <strong>{{item.count.first_finish_percent}}%</strong></span>
							<span title="提交率=已提交待审核的任务数/进行中的任务总数"> 提交率 <strong>{{item.count.reply_percent}}%</strong></span>
						</div>
						<div class="part">
							<span class="all">任务总数 <strong class="rexTip">{{item.count.total}}</strong></span>
							<span class="now">进行中 <strong class="rexTip">{{item.count.doing}}</strong></span>
							<span class="remove">超时 <strong class="rexTip">{{item.count.timeout}}</strong></span>
							<span class="remove">已撤销 <strong class="rexTip">{{item.count.repeal}}</strong></span>
						</div>
					</div>
				</div>
				<div class="overview style">
					<h3><span class="fa fa-paste"></span>请示统计</h3>
					<div class="inside">
						<div class="part">
							<span class="all">请示总数 <strong class="rexTip all">123</strong></span>
							<span class="now">进行中 <strong class="rexTip now">120</strong></span>
							<span class="remove">已撤销 <strong class="rexTip remove">0</strong></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/config.js"></script>
<script>
var vu=new Vue({
	el: "#app",
	data:{
		task:[],
		consult:[],
		load1:false,
		load2:false,
		viewindex:0
	},
	methods:{
		AJAXbefore: function(){

		},
		AJAXerror: function(code,msg){

		},
		AJAXsuccess: function(data){
			this.task=data.data;
		},
		getTask: function(){
			ajax1.send();
		},
		changeView: function(index){
			if (this.viewindex==index) return;
			this.viewindex=index;
		}
	}
});
var ajax1=new relaxAJAX();
var ajax2=new relaxAJAX();
ajax1.url=URL.taskover;
ajax1.before=vu.AJAXbefore;
ajax1.error=vu.AJAXerror;
ajax1.success=vu.AJAXsuccess;
vu.getTask();
</script>
</body>
</html>