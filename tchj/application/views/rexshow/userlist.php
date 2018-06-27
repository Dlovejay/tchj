<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>用户管理</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
<style type="text/css">

</style>
<body>
<div id="app" class="outFrame rexFrame nb nl header">
	<div class="rexTopbar">
		<h2 class="bkStyle2"><span class="fa fa-group"></span> 用户管理</h2>
		<span class="tools">
			<span class="count">当前共 <strong>{{list.length}}</strong> 个用户</span>
			<button class="rexButton ss fa fa-plus warning"></button>
		</span>
	</div>
	<div class="rexRightpart">
		<div class="tableFrame">
			<table class="rexTable fixed">
				<tr>
					<th width="40"></th>
					<th>类型</th>
					<th>用户名</th>
					<th>部门</th>
					<th>职务</th>
					<th>姓名</th>
					<th>电话</th>
					<th width="60">操作</th>
				</tr>
				<tr v-for="(item,index) in list">
					<td>{{index+1}}</td>
					<td>{{item.tname}}</td>
					<td>{{item.username}}</td>
					<td>{{item.pname || '--'}}</td>
					<td>{{item.jname || '--'}}</td>
					<td>{{item.realname}}</td>
					<td>{{item.telnumber}}</td>
					<td><button class="rexButton ss infor fa fa-pencil"></button></td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div id="URL" class="dataField"><?php echo $config['URLSTR']; ?></div>
<div id="user" class="dataField"><?php echo json_encode($user); ?></div>
<div id="account" class="dataField"><?php echo json_encode($account); ?></div>
	<script src="/script/vue-2.5.9.min.js"></script>
	<script src="/script/jquery-1.12.4.min.js"></script>
	<script src="/script/relax_function1.1.1.js"></script>
	<script src="/script/relax_ajax.js"></script>
	<script src="/script/dialog1.3.1.js"></script>
	<script src="/script2/config.js"></script>
<script>
var vu=new Vue({
  el:'#app',
  data:{
		list:'',
		me:''
  },
	methods:{
		
	},
	created:function(){
		this.list=JSON.parse($('#user').text());
		this.me=JSON.parse($('#account').text());
	}
});

</script>
</body>
</html>