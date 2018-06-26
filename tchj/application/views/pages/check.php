<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>测试用页面</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
<style type="text/css">

</style>
</head>
<body>
	<div id="app" class="outFrame">
		<table v-if="len>0">
			<tr>
				<th v-for="item in title">{{item}}</th>
			<tr>
			<tr v-for="item in list">
				<td v-for="key in title">{{item[key]}}</td>
			</tr>
		</table>
		<div v-if="len<=0">没有任何数据信息</div>
	</div>
<div id="data" class="dataField"><?php echo json_encode($data); ?></div>
<script src="/script/vue-2.5.9.min.js"></script>
<script src="/script/jquery-1.12.4.min.js"></script>
<script src="/script/relax_function1.1.1.js"></script>
<script src="/script/relax_ajax.js"></script>
<script src="/script/md5.min.js"></script>
<script src="/script/config.js"></script>
<script>
var vu=new Vue({
  el: "#app",
  data: {
		list:'',
		title:[],
		len:0
  },
	methods:{
		gogo: function(){
			alert("ok");
		}
	},
	created:function(){
		var temp=JSON.parse($('#data').text());
		var isarray=temp instanceof Array;
		if (isarray==false){
			this.list=[temp];
		}else{
			this.list=temp;
		}
		this.len=this.list.length;
		if (this.len>0){
			var temp=this.list[0];
			this.title=[];
			for (var x in temp){
				this.title.push(x);
			}
		}
	}
});
</script>
</body>
</html>