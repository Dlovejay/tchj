<!DOCTYPE html>
<html lang="zh">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="UTF-8">
    <title>管理系统主页</title>
    <link rel="stylesheet" href="/style/fontawesome/font-awesome.css"/>
    <link rel="stylesheet" href="/style/publicStyle5.1.css"/>
    <link rel="stylesheet" href="/style/main.css?1/">
</head>
<body>
<div id="app" class="outFrame">
    <div class="pageHeader leftshow">
        <h2 class="fa fa-calendar"> 请示列表</h2>
        <span class="rexLabel t1">共找到 <strong><?=$total?></strong> 条任务信息&ensp;</span>
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
<div id="user" class="dataField"><?php echo json_encode($user); ?></div>
<script src="/script/vue-2.5.9.min.js"></script>
<script src="/script/jquery-1.12.4.min.js"></script>
<script src="/script/relax_function1.1.1.js"></script>
<script src="/script/relax_ajax.js"></script>
<script src="/script/config.js"></script>
<script>
    $(function(){
        var temp=$('.rexLeftpart>a');
        //菜单栏的
        $(temp).click(function(){
            clearStatus();
            $(this).addClass('sel');
        });
        //我的账户
        $("#myaccount").click(function(){
            clearStatus();
        });
        function clearStatus(){
            $(temp).removeClass('sel');
        }
        //版本号显示
        $(".ver").text(CFG.ver);
    });
</script>
</body>
</html>
