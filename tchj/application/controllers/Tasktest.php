<?php

class Tasktest extends MY_Controller{
		public function __construct(){
				parent::__construct();
		}


		// 默认页面
		public function index(){
				$this->load->model('User');
				$result=$this->User->user();
				$data['user']=$result['data'];
				$data['account']=$_SESSION['user'];
				$this->load->model('Base');
				$result=$this->Base->department();
				$data['department']=$result['data'];
				$this->load->view('task/tasklist.php',$data);
		}
	
		//获得任务列表
		public function lists(){
				$user = $this->session->userdata('user');
				$params = array(
						'keywords' => $this->input->post('keywords'),
						'status' => $this->input->post('status'),
						'is_timeout' => $this->input->post('is_timeout'),
						'page' => $this->input->post('page'),
						'pagesize' => $this->input->post('pagesize'),
				);
				$this->load->model('TaskModel');
				rexAjaxReturn(0, '', $this->TaskModel->getPageResult($user, $params));
		}
		
		// 获取明细
    public function detail() {
        $user = $this->session->userdata('user');
        $id = $this->input->post('id');
        if (empty($id)) {
            rexAjaxReturn(401, "任务id错误");
            return;
        }

        $this->load->model('TaskModel');
        $task = $this->TaskModel->getOneById($id);
        if(empty($task)) {
            rexAjaxReturn(401, "任务不存在");
            return;
        }

        $this->TaskModel->toUnstartStatus($task);
        $data = $this->TaskModel->getOperation($task, $user);
        if (!$data["success"]) {
            rexAjaxReturn("403", "无权限查看");
            return;
        }
        $dos = $data["do"];
        $timeout = $this->TaskModel->isTimeout($task);

        rexAjaxReturn(0, "",  array("replys" => json_decode($task["replys"], true), "is_timeout" => $timeout, "do" => $dos));
    }
	
		// 发起任务
		public function add() {
				$user=$this->session->userdata('user');
				$result=$result=parent::checkPower([USERL,USERU]);
				if ($result["code"] != 0) {
						rexAjaxReturn($result);
						return;
				}

				$result = $this->check($user, $this);
				if (!$result["success"]) {
						rexAjaxReturn(401, $result["errors"]);
						return;
				}

				$fields = &$result["data"];
				$fields["create_user_id"] = $user["uid"];

				$this->load->model('TaskModel');
				$id = $this->TaskModel->Add($fields);
				if ($id == 0) {
						rexAjaxReturn(500, "插入失败",  array("id" => $id));
				} else {
						rexAjaxReturn(0, "",  array("id" => $id));
				}
		}
	
		// 验证 插入和修改的数据
		private function check($user, $self) {
				$this->load->library('form_validation');

				$data = array(
						'title' => $this->input->post('title'),
						//'mtitle' => $this->input->post('mtitle'), //先放一放
						'content' => $this->input->post('content'),
						'start_at' => $this->input->post('start_at'),
						'end_at' => $this->input->post('end_at'),
						'annex' => $this->input->post('annex'),
						'departments' => $this->input->post('departments'),
				);

				//print_r($data);exit;

				$config = array(
						array(
								'field' => 'title',
								'label' => '标题',
								'rules' => 'trim|required',
						),
						array(
								'field' => 'content',
								'label' => '内容',
								'rules' => 'required',
						),
						array(
								'field' => 'start_at',
								'label' => '开始时间',
								'rules' => 'required|integer|less_than['.$data["end_at"].']',
								"errors" => array(
										"less_than" => "开始时间必须小于结束时间",
								)
						),
						array(
								'field' => 'end_at',
								'label' => '结束时间',
								'rules' => 'required|integer',
						),
						array(
								'field' => 'departments',
								'label' => '部门',
								'rules' => array(
										'required',
										array(
												"anonymous",
												function($value) use ($user, $self) {
														$ids = explode(",", $value);
														$ids = array_unique($ids);
														$levels = array();
														if($user["tid"] == 2) {
																if (count($ids) == 1 && $ids[0] === "0") {
																		return true;
																}
																$levels = array(1,2);
														} else if ($user["tid"] == 3) {
																$levels = array(2);
														} else {
																return false;
														}

														$self->load->model('Department');
														$departments = $self->Department->getListByLevels($levels);
														$departmentIds = array();
														foreach($departments as $d) {
																$departmentIds[] = $d["pid"];
														}
														$tmpIds = array_intersect($departmentIds, $ids);
														if (count($tmpIds) == count($ids)) {
																return true;
														} else {
																return false;
														}
												}
										)
								),
								"errors" => array (
										"anonymous" => "部门没权限",
								)
						),
						array (
								'field' => 'annex',
								'label' => '附件',
								'rules' => array(
										array (
												"anonymous",
												function ($value) {
														if (isset($value) && trim($value) != "") {
																$result = json_decode($value, true);
																if (!isset($result) || !is_array($result)) {
																		return false;
																}
														}
														return true;
												}
										)
								),
								"errors" => array (
										"anonymous" => "附件格式错误",
								)
						)
				);


				$this->form_validation->set_data($data);
				$this->form_validation->set_rules($config);

				if ($this->form_validation->run() == false) {
						$errors = $this->form_validation->error_array();
						$firstErrorMsg = "";
						foreach($errors as $v) {
								$firstErrorMsg = $v;
								break;
						}
						return array("success" => false, "errors" => $firstErrorMsg);
				} else {
						if (!isset($data["annex"]) || trim($data["annex"]) === "") {
								$data["annex"] = array();
						} else {
								$data["annex"] = json_decode($data["annex"], true);
						}

						$data["departments"] = explode(",", $data["departments"]);

						return array("success" => true, "data" => $data);
				}
		}
}