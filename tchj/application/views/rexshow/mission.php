<!DOCTYPE html>
<html lang="zh">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="UTF-8">
	<title>任务列表</title>
	<link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
	<link rel="stylesheet" href="/style/publicStyle5.1.css"/>
<style type="text/css">
.nowStatus{ padding:3px 5px; border-radius:6px; font-weight:normal; border:1px solid #ccc;}
.nowStatus.st0{ background-color:#999; border-color:#999;}
.nowStatus.st0:before{ content:'未开始'; color:#eee;}
.nowStatus.st1{ background-color:#fff3dc; border-color:#f90;}
.nowStatus.st1:before{ content:'待接受'; color:#f90; font-weight:600;}
.nowStatus.st2{ background-color:#dae7ff; border-color:#3362b8;}
.nowStatus.st2:before{ content:'处理中'; color:#3362b8;}
.nowStatus.st3{ background-color:#fff3dc; border-color:#f90;}
.nowStatus.st3:before{ content:'待评审'; color:#f90; font-weight:600;}
.nowStatus.st4{ background-color:#1ba50c; border-color:#1ba50c;}
.nowStatus.st4:before{ content:'已完成'; color:#fff;}
.nowStatus.st5{ background-color:#e44e4e; border-color:#c00505;}
.nowStatus.st5:before{ content:'被退回'; color:#fff; font-weight:600;}
.nowStatus.st6{ background-color:#b4b4b4; border:1px dashed #5a5a5a;}
.nowStatus.st6:before{ content:'已撤销'; color:#333;}
.timeoutStatus{ background-color:red; color:#fff; padding:3px 5px; border-radius:6px; font-weight:normal; border:1px solid red;}

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
			<h2 class="bkStyle2"><span class="fa fa-calendar"></span> 任务管理</h2>
			<span class="tools">
				<span class="count">共查找到 <strong>{{list.length}}</strong> 条任务信息</span>
				<button class="rexButton ss fa fa-plus warning" title="新增任务" v-if="isPublic" @click="showDialog('inforOP')"></button>
				<button class="rexButton ss fa fa-search infor" title="打开查询条件编辑" @click="showDialog('selreal')"></button>
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
					<td>{{(real.nowpage-1)*CFG.pagesize+index+1}}</td>
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
						<span class="fa " v-bind:class="chk[0].flag">&ensp;{{chk[0].msg}}</span>
					</td>
				</tr>
			</table>
			</div>
			<!-- 分页-->
			<ul class="cutpage noHead t1" v-if="real.pages>0">
				<li class="rexButton ss" v-for="n in real.pages" v-bind:class="{'infor':n!=real.nowpage,'warning':n==real.nowpage}" v-bind:disabled="load.list" @click="gopage(n)">{{n}}</li>
			</ul>
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
									<option value="0">未发布</option>
									<option value="1">待接受</option>
									<option value="2">处理中</option>
									<option value="3">待评审</option>
									<option value="4">已完成</option>
									<option value="5">退回</option>
									<option value="6">撤销</option>
								</select>
							</span>
						</li>
						<li class="formpart">
							<label class="rexLabel">超&emsp;时</label><span>
								<select class="rexSelect" v-model="real2.timeout">
									<option value=""></option>
									<option value="1">是</option>
									<option value="0">否</option>
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
						<button class="rexButton opBnt infor" @click="sendData()"> 提交回复</button>
					</div>
				</div>
				<div class="dialog-content">
					<ul class="lay2col style1">
						<li class="formpart alone" v-if="viewobj.status==3 && bs1">
							<label class="rexLabel">任务评审</label><span>
								<span class="rexCheck">
									<input type="radio" name="returntype" value="1" checked="checked"/>
									<label>任务完成</label>
								</span>
								<span class="rexCheck">
									<input type="radio" name="returntype" value="0"/>
									<label>审核不通过，退回</label>
								</span>
							</span>
						</li>
						<li class="formpart alone">
							<label class="rexLabel">回复内容</label><span class="request">
								<textarea class="rexTxtarea"></textarea>
							</span>
						</li>
						<li class="formpart alone" v-if="viewobj.timeout==1 && viewobj.status==2 && bs1">
							<label class="rexLabel">超时说明</label><span class="request">
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
					<span class="opBnt right fa fa-lg fa-times" v-if="load.re==false" @click="hideDialog('viewOP')"></span>
					<span class="opBnt left fa fa-lg fa-pencil" title="修改当前任务信息" v-if="viewobj.authorid==me.uid && load.re==false" @click="showDialog('inforOP')"></span>
					<h4 class="t3"><span class="fa fa-eye"></span>&emsp;<span class="diy">查看任务详情</span></h4>
				</div>
				<div class="dialog-content">
					<div class="datafill">
						<ul class="lay2col">
							<li class="formpart alone view">
								<label class="rexLabel">任务状态</label><span>
									<strong class="nowStatus" v-bind:class="'st'+viewobj.status"></strong>
									<strong class="timeoutStatus" v-if="viewobj.timeout=='1'">任务超时</strong>
								</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">任务标题</label><span>{{viewobj.mtitle}}</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">发布部门</label><span>{{viewobj.tips?viewobj.tips:'--'}}</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">开始日期</label><span>{{viewobj.datestart}}</span>
							</li>
							<li class="formpart view">
								<label class="rexLabel">截止日期</label><span>{{viewobj.dateend?viewobj.dateend:'--'}}</span>
							</li>
							<li class="alone formpart view">
								<label class="rexLabel">任务说明</label><span>{{viewobj.content}}</span>
							</li>
							<li class="alone formpart view">
								<label class="rexLabel">指派部门</label><span>{{viewobj.pass}}</span>
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
										<td colspan="3" class="tipMessage warning tCT"><span class="fa fa-lg fa-warning"></span> 暂无任何列表信息</td>
									</tr>
								</table>
							</li>
							<li class="alone formpart table returnList">
								<div class="captionTitle">
									<span class="fa fa-comments-o"> 回复列表</span>
								</div>
								<div v-for="item in returnList[viewobj.mid]" v-bind:class="item.classstr">
									<span class="reuser"><strong>{{item.username}}</strong>{{item.datemake}}</span>
									<div class="recontent">{{item.content}}</div>
								</div>
								<div class="anmanager">
									<span class="reuser"><strong>manager</strong> 2018-06-34</span>
									<div class="recontent">回复内容回复内容回复内容回复内容回复内容回复内容回复内容</div>
								</div>
							</li>
						</ul>
					</div>
					<div class="buttonBar" v-if="me.tid!=CFG.UM">
						<button class="rexButton infor" v-if="bs4">接受任务</button>
						<button class="rexButton infor" @click="showDialog('answer')" v-if="bs1">我要回复</button>
						<button class="rexButton alert" @click="showDialog('sure')" v-if="bs3">删除任务</button>
						<button class="rexButton alert" @click="showDialog('sure')" v-if="bs2">撤销任务</button>
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
					<div class="datafill">
						<ul class="lay2col">
							<li class="formpart">
								<label class="rexLabel">任务标题</label><span class="request">
									<input type="text" class="rexInput" v-bind:class="{'warning':chk[1].obj=='title'}" v-model.trim="edit.title" autocomplete="off" maxlength="50"/>
								</span>
							</li>
							<li class="formpart">
								<label class="rexLabel">发布部门</label><span>
									<input type="text" class="rexInput" v-bind:class="{'warning':chk[1].obj=='tips'}" v-model.trim="edit.tips" autocomplete="off" maxlength="20"/>
								</span>
							</li>
							<li class="formpart">
								<label class="rexLabel">开始日期</label><span class="request">
									<input type="date" v-bind:class="{'warning':chk[1].obj=='datestart'}" class="rexInput" v-model.trim="edit.datestart"/>
								</span>
							</li>
							<li class="formpart">
								<label class="rexLabel">截止日期</label><span title="不填写该项表示当前任务无日期限制">
									<input type="date" v-bind:class="{'warning':chk[1].obj=='dateend'}" class="rexInput" v-model.trim="edit.dateend"/>
								</span>
							</li>
							<li class="alone formpart">
								<label class="rexLabel">任务说明</label><span class="request">
									<textarea class="rexTxtarea" v-bind:class="{'warning':chk[1].obj=='intro'}" v-model.trim="edit.intro"></textarea>
								</span>
							</li>
							<li class="alone formpart">
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
									<span class="rexTurn infor" v-for="item in getDpmt" v-bind:disabled="edit.powertype==0">
										<input type="checkbox" name="powdpmt" v-bind:value="item.pid" v-model="edit.power"/>
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
	
	<div id="FTP" class="dataField"><?php echo '' ?></div>
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
	<script src="/script2/mission.js"></script>
</body>
</html>