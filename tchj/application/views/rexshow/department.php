<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>部门管理</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
<style type="text/css">
</style>
<body>
<div id="app" class="outFrame">
	<div class="formblock" v-for="(item,key) in level">
		<label class="rexLabel">{{levelName[key]}}</label>
		<span>
			<button class="rexButton" v-for="dpt in item">{{dpt.pname}}</button>
		</span>
	</div>
</div>
<div id="URL" class="dataField"><?php echo $config['URLSTR']; ?></div>
<div id="department" class="dataField"><?php echo json_encode($department); ?></div>
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
		level:{},
		levelName:{
			'0':'支队名称',
			'1':'上级部门',
			'2':'下级部门'
		}
  },
	methods:{
		
	},
	created:function(){
		var temp=JSON.parse($('#department').text());
		this.list=JSON.array2Object(temp,'pid');
		var key;
		for (var i=0; i<temp.length; i++){
			key=temp[i].plevel;
			if (!this.level[key]) this.level[key]=[];
			this.level[key].push(this.list[temp[i].pid]);
		}
	}
});
</script>
</body>
</html>