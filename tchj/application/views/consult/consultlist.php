<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>太仓海警支队--请示列表</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.2.css"/>
	<link rel="stylesheet" href="/style/consult.css"/>
<body>
	<div id="app" class="outFrame rexFrame nb nl header">
		<div class="rexTopbar">
			<h2 class="bkStyle2"><span class="fa fa-paste"></span> 请示列表</h2>
			<span class="tools">
				<span class="count">共查找到 <strong>{{list.length}}</strong> 条请示信息</span>
				<button class="rexButton ss fa fa-plus warning" title="新增请示" v-if="isPublic" @click="showDialog('consultop')"></button>
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
						<td>{{(pager.page-1)*pager.pagesize+index+1}}</td>
						<td><strong class="nowStatus" v-bind:class="'st'+item.status"></strong></td>
						<td><span class="txt">{{item.title}}</span></td>
						<td>{{item.datemake}}</td>
						<td>{{item.author}}</td>
						<td>{{item.annex.length}}</td>
						<td>{{item.rcount}}</td>
						<td>
							<button class="rexButton ss fa fa-eye infor" @click="showDialog('viewOP',item.cid)"> 查看</button>
						</td>
					</tr>
					<tr class="alone" v-if="chk[0].flag!=''">
						<td colspan="9" class="tipMessage" v-bind:class="chk[0].flag">
							<span class="fa " v-bind:class="chk[0].flag">&ensp;{{chk[0].msg}}</span>
						</td>
					</tr>
				</table>
			</div>
			<!-- 分页-->
			<ul class="cutpage noHead t1" v-if="pager.pagecount>1">
				<li class="rexButton ss" v-for="n in pager.pagecount" v-bind:class="{'infor':n!=pager.page,'warning':n==pager.page}" v-bind:disabled="load.list" @click="getAJAXList(n)">{{n}}</li>
			</ul>
		</div>
		
		<!-- 添加/编辑任务页面-->
		<div id="consultop" class="extPage">
			<div class="dialogFrame">
				<div class="dialog-title">
					<h4 class="t3"><span class="fa fa-plus" v-if="op=='add'"> 新增请示信息</span><span class="fa fa-pencil" v-if="op=='edit'"> 编辑请示信息</span></h4>
				</div>
				<div class="dialog-content">
					<div class="datafill">
						<ul class="lay2col">
							<li class="formpart">
								<label class="rexLabel">请示标题</label><span class="request">
									<input type="text" class="rexInput" v-bind:class="{'warning':chk[1].obj=='title'}" v-model.trim="edit.title" autocomplete="off" maxlength="50"/>
								</span>
							</li>
							<li class="alone formpart">
								<label class="rexLabel">请示内容</label><span class="request">
									<textarea class="rexTxtarea"  v-bind:class="{'warning':chk[1].obj=='content'}" v-model.trim="edit.content"></textarea>
								</span>
							</li>
							<li class="alone formpart">
								<label class="rexLabel">投&ensp;递&ensp;给</label><span>
									<span class="rexTurn infor" v-for="item in getDispatchDepartment">
										<input type="radio" name="powdpmt" v-bind:value="item.pid" v-model="edit.pid"/>
										<label>{{item.pname}}</label>
									</span>
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
						<div class="tipMessage" v-if="chk[1].flag" v-bind:class="chk[1].flag">
							<span class="fa fa-lg" v-bind:class="chk[1].flag"> {{chk[1].msg}}</span>
						</div>
						<button class="rexButton" @click="hideDialog('consultop')" v-bind:disabled="load.op">取 消</button>
						<button class="rexButton infor" @click="getAJAXAdd()" v-bind:disabled="load.op">提 交</button>
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
								<span v-if="axTemp.type=='1'">{{cfg.FTP}}</span>
								<input type="text" class="rexInput" v-bind:class="{'warning':chk[2].obj=='url'}" v-model="axTemp.url"/>
							</span>
						</div>
						<div class="formpart alone">
							<label class="rexLabel">附件名称</label><span>
								<input type="text" class="rexInput" v-model="axTemp.name"/>
							</span>
						</div>
						<div class="tipMessage" v-if="chk[2].flag">
							<span class="fa" v-bind:class="chk[2].flag">&ensp;{{chk[2].msg}}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
					
	</div>
	
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
	<script src="/script/consult.js"></script>
</body>
</html>