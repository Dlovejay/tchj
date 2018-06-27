<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>职务管理</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
<style type="text/css">
.formpart>span .rexButton{ margin:3px;}
</style>
<body>
<div id="app" class="outFrame rexFrame nb nl header">
	<div class="rexTopbar">
		<h2 class="bkStyle1 tCT"><span class="fa fa-list-alt"></span> 职务管理</h2>
	</div>
	<div class="rexRightpart">
		<div class="formpart" v-for="item in department">
			<label class="rexLabel">{{item.name}}</label><span>
				<button class="rexButton" v-for="job in item.data">{{job.jname}}</button>
			</span>
		</div>
	</div>
</div>
<div id="URL" class="dataField"><?php echo $config['URLSTR']; ?></div>
<div id="job" class="dataField"><?php echo json_encode($job); ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/config.js"></script>
<script>
var vu=new Vue({
  el:'#app',
  data:{
		list:{},
		department:{}
  },
	methods:{
		
	},
	created:function(){
		var temp=JSON.parse($('#job').text());
		this.list=JSON.array2Object(temp,'jid');
		var key;
		for (var i=0; i<temp.length; i++){
			key=temp[i].pid;
			if (!this.department[key]) this.department[key]={name:temp[i]['pname'],data:[]};
			this.department[key].data.push(this.list[temp[i].jid]);
		}
	}
});
</script>
</body>
</html>