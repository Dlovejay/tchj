<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>江苏海警支队--任务列表</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.2.css"/>
	<link rel="stylesheet" href="/style/task.css"/>
<body>
	<div id="app" class="outFrame rexFrame nb nl header">
		<div class="rexTopbar">
			<h2 class="bkStyle2"><span class="fa fa-calendar"></span> 任务管理</h2>
			<span class="tools">
				<span class="count">共查找到 <strong>{{pager.total}}</strong> 条任务信息</span>
				<button class="rexButton ss fa fa-plus warning" title="新增任务" v-if="isPublic" @click="showDialog('taskop')"></button>
				<button class="rexButton ss fa fa-search infor" title="查询任务" @click="showDialog('selreal')"></button>
			</span>
		</div>
		
		<div class="rexRightpart">
			<div class="tableFrame">
				<table class="rexTable fixed">
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
						<td>{{(pager.page-1)*pager.pagesize+index+1}}</td>
						<td><strong class="nowStatus" v-bind:class="'st'+item.status"></strong></td>
						<td><span class="txt"><strong class="tips">[{{item.pub}}]&ensp;</strong>{{item.title}}</span></td>
						<td><span class="txt t1 fa fa-lg" v-bind:class="{'fa-clock-o':item.timeout}"> {{item.date}}</span></td>
						<td>{{item.author}}</td>
						<td><span class="txt">{{item.department}}</span></td>
						<td>{{item.annex}}</td>
						<td>{{item.count}}</td>
						<td>
							<button class="rexButton ss fa fa-eye infor" @click="showDialog('viewop',item.mid)"> 查看</button>
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
		<div id="taskop" class="extPage">
			<div class="dialogFrame">
				<div class="dialog-title">
					<h4 class="t3"><span class="fa fa-plus" v-if="op=='add'"> 新增任务信息</span><span class="fa fa-pencil" v-if="op=='edit'"> 编辑任务信息</span></h4>
				</div>
				<div class="dialog-content">
					<div class="datafill">
						<ul class="lay2col">
							<li class="formpart">
								<label class="rexLabel">任务标题</label><span class="request">
									<input type="text" class="rexInput" v-bind:class="{'warning':chk[1].obj=='title'}" v-model.trim="edit.title" autocomplete="off" maxlength="50"/>
								</span>
							</li>
							<li class="formpart">
								<label class="rexLabel">发布部门</label><span>
									<select class="rexSelect" v-bind:class="{'warning':chk[1].obj=='mtitle'}" v-model="edit.mtitle">
										<option v-for="item in getPublicDepartment" v-bind:value="item.pid">{{item.pname}}</option>
									</select>
								</span>
							</li>
							<li class="formpart">
								<label class="rexLabel">开始日期</label><span class="request">
									<input type="date" v-bind:class="{'warning':chk[1].obj=='start_at'}" class="rexInput" v-model.trim="edit.start_at"/>
								</span>
							</li>
							<li class="formpart">
								<label class="rexLabel">截止日期</label><span class="request">
									<input type="date" v-bind:class="{'warning':chk[1].obj=='end_at'}" class="rexInput" v-model.trim="edit.end_at"/>
								</span>
							</li>
							<li class="alone formpart">
								<label class="rexLabel">任务说明</label><span class="request">
									<textarea class="rexTxtarea" v-bind:class="{'warning':chk[1].obj=='content'}" v-model.trim="edit.content"></textarea>
								</span>
							</li>
							<li class="alone formpart">
								<label class="rexLabel">指派部门</label><span>
									<span>
										<span class="rexCheck">
											<input type="radio" name="choosetype" value="0" v-model="edit.isAll"/>
											<label>全部</label>
										</span>
										<span class="rexCheck">
											<input type="radio" name="choosetype" value="1" v-model="edit.isAll"/>
											<label>自定义</label>
										</span><br/>
									</span>
									<span class="rexTurn infor" v-for="item in getDispatchDepartment" v-bind:disabled="edit.isAll==0">
										<input type="checkbox" name="powdpmt" v-bind:value="item.pid" v-model="edit.department"/>
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
						<button class="rexButton" @click="hideDialog('taskop')" v-bind:disabled="load.op">取 消</button>
						<button class="rexButton infor" @click="getTaskSend()" v-bind:disabled="load.op">提 交</button>
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
			
		<!-- 查看详情弹出页面 -->
		<div id="viewop" class="extPage">
			<div class="dialogFrame">
				<div class="dialog-title">
					<span class="opBnt right fa fa-lg fa-times" v-if="load.re==false" @click="hideDialog('viewop')"></span>
					<span class="opBnt left fa fa-lg fa-pencil" title="修改当前任务信息" v-if="canDo && canEdit" @click="showDialog('taskop')"></span>
					<h4 class="t3"><span class="fa fa-eye"></span>&emsp;<span class="diy">查看任务详情</span></h4>
				</div>
				<div class="dialog-content">
					<div class="datafill">
						<ul class="lay2col">
							<li class="formpart alone view">
								<label class="rexLabel">任务状态</label><span>
									<strong class="nowStatus" v-bind:class="'st'+viewobj.status"></strong>
									<strong class="timeoutStatus" v-if="viewobj.timeout">任务超时</strong>
								</span>
							</li>
							<li class="formpart alone view" v-if="viewobj.timeout">
								<label class="rexLabel">超时原因</label><span>{{viewobj.cause}}</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">任务标题</label><span>{{viewobj.title}}</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">发布部门</label><span>{{viewobj.pub}}</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">开始日期</label><span>{{viewobj.start_at}}</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">截止日期</label><span>{{viewobj.end_at}}</span>
							</li>
							<li class="alone formpart view">
								<label class="rexLabel">任务说明</label><span v-html="viewobj.content"></span>
							</li>
							<li class="alone formpart view">
								<label class="rexLabel">指派部门</label><span>{{viewobj.department}}</span>
							</li>
							<li class="alone formpart table">
								<table class="rexRowtable notitle fixed">
									<caption class="captionTitle">
										<span class="fa fa-chain"> 附件列表</span>
									</caption>
									<tr v-for="(item,index) in viewobj.annexobj">
										<td width="35">{{index+1}}</td>
										<td><a v-bind:href="getFileURL(item)" target="_blank">{{item.name}}</a></td>
										<td width="10">
										</td>
									</tr>
									<tr class="alone" v-if="viewobj && viewobj.annexobj.length==0">
										<td colspan="3" class="tipMessage tCT"><span class="fa warning"> 暂无任何附件信息</span></td>
									</tr>
								</table>
							</li>
							<li class="alone formpart table returnList" v-if="viewobj.mid && returnList[viewobj.mid]">
								<div class="captionTitle">
									<span class="fa fa-comments-o"> 回复列表</span>
									<button class="rexButton fa fa-refresh ss infor" title="刷新回复列表" v-bind:disabled="load.re" @click="getAJAXDetail()"></button>
								</div>
								<div v-for="item in returnList[viewobj.mid].list" v-bind:class="item.classstr">
									<span class="reuser"><strong>{{item.create_user_name}}</strong>{{item.update_at}}</span>
									<div class="recontent">{{item.content}}</div>
								</div>
								<div class="tipMessage" v-if="chk[3].flag">
									<span class="fa" v-bind:class="chk[3].flag"> {{chk[3].msg}}</span>
								</div>
							</li>
						</ul>
					</div>
					<div class="buttonBar" v-if="me.tid!=CFG.UM">
						<button class="rexButton infor" @click="showDialog('sure','RECEIVE')" v-if="canDo && canReceive">接受任务</button>
						<button class="rexButton infor" @click="showDialog('answer')" v-if="canDo && canReply">{{getButtonTxt}}</button>
						<button class="rexButton alert" @click="showDialog('sure','DELETE')" v-if="canDo && canDelete">删除任务</button>
						<button class="rexButton alert" @click="showDialog('sure','REPEAL')" v-if="canDo && canRepeal">撤销任务</button>
					</div>
				</div>
			</div>
		</div>
			
		<!-- 确认操作提示信息 -->
		<div id="sure" class="extDialog" noclick="noclick">
			<div class="dialogFrame">
				<div class="dialog-title warning">
					<h4 class="t3"><span class="fa fa-exclamation-circle"></span>&emsp;<span class="diy">系统信息提示</span></h4>
				</div>
				<div class="dialog-buttonBar">
					<div class="bntInside">
						<button class="rexButton" @click="hideDialog('sure')" v-bind:disabled="load.re"> 取 消</button>
						<button class="rexButton infor" @click="getAJAXNext()" v-bind:disabled="load.re"> 确 定</button>
					</div>
				</div>
				<div class="dialog-content">
					<div class="diy sP1"></div>
					<div class="tipMessage" v-if="chk[4].flag">
						<span class="fa" v-bind:class="chk[4].flag"> {{chk[4].msg}}</span>
					</div>
				</div>
			</div>
		</div>
			
		<!-- 回复弹框 -->
		<div id="answer" class="extDialog" noclick="noclick">
			<div class="dialogFrame">
				<div class="dialog-title">
					<span class="opBnt right fa fa-lg fa-times" @click="hideDialog('answer')" v-if="!load.re"></span>
					<h4 class="t3"><span class="fa fa-comment-o"></span>&emsp;<span class="diy">{{getButtonTxt}}</span></h4>
				</div>
				<div class="dialog-buttonBar">
					<div class="bntInside">
						<button class="rexButton opBnt infor" @click="getAJAXNext()" v-bind:disabled="load.re">{{getButtonTxt2}}</button>
					</div>
				</div>
				<div class="dialog-content">
					<ul class="lay2col style1 sP1">
						<li class="formpart alone" v-if="canDo && canFinish">
							<label class="rexLabel">任务评审</label><span>
								<span class="rexCheck" style="margin-right:10px">
									<input type="radio" name="returntype" value="FINISHED" v-model="answer.do"/>
									<label>任务完成</label>
								</span>
								<span class="rexCheck">
									<input type="radio" name="returntype" value="BACK" v-model="answer.do"/>
									<label>审核不通过，退回</label>
								</span>
							</span>
						</li>
						<li class="formpart alone">
							<label class="rexLabel">{{getLabelTxt}}</label><span class="request">
								<textarea class="rexTxtarea" v-model.trim="answer.content" v-bind:class="{'warning':chk[4].obj=='content'}"></textarea>
							</span>
						</li>
						<li class="formpart alone" v-if="canDo && canTimeout">
							<label class="rexLabel">超时说明</label><span class="request">
								<textarea class="rexTxtarea" v-model.trim="answer.cause" v-bind:class="{'warning':chk[4].obj=='cause'}"></textarea>
							</span>
						</li>
					</ul>
					<div class="tipMessage" v-if="chk[4].flag">
						<span class="fa" v-bind:class="chk[4].flag"> {{chk[4].msg}}</span>
					</div>
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
						<button class="rexButton opBnt" @click="clearRealation()" v-bind:disabled="this.load.op"> 重 置</button>
						<button class="rexButton opBnt infor" @click="goSearch()" v-bind:disabled="this.load.op"> 查 询</button>
					</div>
				</div>
				<div class="dialog-content">
					<ul class="lay2col style1 sP1">
						<li class="formpart">
							<label class="rexLabel">标&emsp;题</label><span>
								<input type="text" class="rexInput" v-model.trim="real.keywords" v-bind:class="{'warning':chk[1].obj=='keywords'}" v-bind:disabled="this.load.op"/>
							</span>
						</li>
						<li class="formpart" v-if="filterDPMT.length">
							<label class="rexLabel">指派给</label><span>
								<select class="rexSelect" v-model="real.departmentID">
									<option value=""></option>
									<option v-for="item in filterDPMT" v-bind:value="item.pid">{{item.pname}}</option>
								</select>
							</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">状&emsp;态</label><span>
								<select class="rexSelect" v-model="real.status" v-bind:disabled="this.load.op">
									<option value=""></option>
									<option value="0">未发布</option>
									<option value="1">待接受</option>
									<option value="2">处理中</option>
									<option value="3">待评审</option>
									<option value="4">已完成</option>
									<option value="5">退回</option>
									<option value="7">撤销</option>
								</select>
							</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">超&emsp;时</label><span>
								<select class="rexSelect" v-model="real.is_timeout" v-bind:disabled="this.load.op">
									<option value=""></option>
									<option value="TRUE">是</option>
									<option value="FALSE">否</option>
								</select>
							</span>
						</li>
					</ul>
					<div class="tipMessage" v-if="chk[1].flag" v-bind:class="chk[1].flag">
						<span class="fa fa-lg" v-bind:class="chk[1].flag"> {{chk[1].msg}}</span>
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
	<script src="/script/task.js"></script>
</body>
</html>