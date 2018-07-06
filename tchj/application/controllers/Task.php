<?php
/**
 * Created by PhpStorm.
 * User: taojin
 * Date: 2018/6/27
 * Time: 下午9:09
 */

class Task extends MY_Controller{

    public function __construct(){
        parent::__construct();
    }


    public function statistics() {
        $user = $_SESSION['user'];
        $this->load->model('TaskModel');
        rexAjaxReturn(0, "", $this->TaskModel->GetStatistics($user));
    }

    // 默认页面
    public function index() {
        $this->load->model('User');
        $result=$this->User->user();
        $data['user']=$result['data'];
        $data['account']=$_SESSION['user'];
        $this->load->model('Base');
        $result=$this->Base->department();
        $data['department']=$result['data'];
        $this->load->view('task/tasklist.php',$data);
    }


    public function lists() {
        //$user = $this->session->userdata('user_info');
        $user = $_SESSION['user'];
        $params = array(
            'keywords' => $this->input->post('keywords'),
            'status' => $this->input->post('status'),
            'is_timeout' => $this->input->post('is_timeout'),
            'page' => $this->input->post('page'),
            'pagesize' => $this->input->post('pagesize'),
        );
        $this->load->model('TaskModel');
        rexAjaxReturn(0, "", $this->TaskModel->getPageResult($user, $params));
    }


    // 获取明细
    public function detail() {
        $user = $_SESSION['user'];
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
        $canEdit = $this->TaskModel->canEdit($task, $user);
        rexAjaxReturn(0, "",  array("replys" => json_decode($task["replys"], true), "is_timeout" => $timeout, "do" => $dos, 'canEdit' => $canEdit));
    }



    // 发起任务
    public function add() {
        $user = $_SESSION['user'];
        $result = $this->checkPower(array(USERL, USERU));
        if ($result["code"] != 0) {
            rexAjaxReturn($result["code"], $result["message"]);
            return;
        }

        $result = $this->check($user, $this);
        if (!$result["success"]) {
            rexAjaxReturn(401, $result["errors"]);
            return;
        }

        $fields = &$result["data"];
        $fields["create_user_id"] = $user["uid"];
        $fields['pid'] = $user['pid'];

        $this->load->model('TaskModel');
        $id = $this->TaskModel->Add($fields);
        if ($id == 0) {
            rexAjaxReturn(500, "插入失败",  array("id" => $id));
        } else {
            rexAjaxReturn(0, "",  array("id" => $id));
        }
    }

    // 编辑任务
    public function edit() {
        $user = $_SESSION['user'];
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

        if($task["create_user_id"] != $user["uid"]) {
            rexAjaxReturn(403, "无权操作此任务");
            return;
        }

        if($task["status"] != 1) {
            rexAjaxReturn(401, "无法修改此任务");
            return;
        }


        $result = $this->check($user, $this);
        if (!$result["success"]) {
            rexAjaxReturn(401, $result["errors"]);
            return;
        }

        $fields = &$result["data"];
        if ($this->TaskModel->edit($id, $fields)) {
            rexAjaxReturn();
        } else {
            rexAjaxReturn(500, "更新失败",  array("id" => $id));
        }
    }


    public function next() {
        $user = $_SESSION['user'];

        $result = $this->checkNext();
        if (!$result["success"]) {
            rexAjaxReturn(401, $result["errors"]);
            return;
        }
        $params = $result["data"];

        $this->load->model('TaskModel');
        $task = $this->TaskModel->getOneById($params["id"]);
        if(empty($task)) {
            rexAjaxReturn(401, "任务不存在");
            return;
        }

        $this->TaskModel->toUnstartStatus($task);
        $isTimeout = $this->TaskModel->isTimeout($task);
        if ($params["do"] == "REPLY" && $isTimeout && $task["status"] == 2 ) {
            if ($params["cause"] == "") {
                rexAjaxReturn(401, "请填过期原因");
                return;
            }
        } else {
            unset($params["cause"]);
        }

        $result = $this->TaskModel->next($user, $params, $task, $isTimeout);
        if (!$result["success"]) {
            rexAjaxReturn(403, '无权限操作');
            return;
        }

        //获取新的状态
        $task = $this->TaskModel->getOneById($task['mid']);
        if(empty($task)) {
						//删除任务
						rexAjaxReturn(0,"");
						return;
            //rexAjaxReturn(401, "任务不存在");
            //return;
        }

        $this->TaskModel->toUnstartStatus($task);
        $data = $this->TaskModel->getOperation($task, $user);
        if (!$data["success"]) {
            rexAjaxReturn("403", "无权限查看");
            return;
        }
        $dos = $data["do"];
        $timeout = $this->TaskModel->isTimeout($task);
        $canEdit = $this->TaskModel->canEdit($task, $user);

        rexAjaxReturn(0,"",array("is_timeout" => $timeout, "do" => $dos, 'canEdit' => $canEdit, 'status'=>$task['status']));
    }


    private function checkNext() {
        $this->load->library('form_validation');

        $data = array(
            'id' => $this->input->post('id'),
            'content' => $this->input->post('content'),
            'do' => $this->input->post('do'),
            'cause' => $this->input->post('cause'),
        );

        $config = array(
            array(
                'field' => 'id',
                'label' => '任务id',
                'rules' => 'required|integer',
            ),
            array(
                'field' => 'content',
                'label' => '内容',
                'rules' => 'required',
            ),
            array(
                'field' => 'do',
                'label' => '操作',
                'rules' => 'required',
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
            return array("success" => true, "data" => $data);
        }
    }


    // 验证 插入和修改的数据
    private function check($user, $self) {
        $this->load->library('form_validation');
        $self->load->model('Department');

        $data = array(
            'title' => $this->input->post('title'),
            'content' => $this->input->post('content'),
            'start_at' => $this->input->post('start_at'),
            'end_at' => $this->input->post('end_at'),
            'annex' => $this->input->post('annex'),
            'departments' => $this->input->post('departments'),
            'initiate_pid' => $this->input->post('initiate_pid'),
        );

        if (empty($data['initiate_pid'])) {
            $data['initiate_pid'] = 0;
        }

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
            ),
            array(
                'field' => 'initiate_pid',
                'label' => '发起部门id',
                'rules' => array(
                    'integer',
                    array (
                        "anonymous",
                        function ($value) use ($user, $self) {
                            if ($user["tid"] == 2 && $value != 0) {
                                return false;
                            }

                            if ($user["tid"] == 3) {
                                $departments = $self->Department->getListByLevels(array(1));
                                $departmentIds = array();
                                foreach($departments as $d) {
                                    $departmentIds[] = $d["pid"];
                                }

                                if (!in_array($value, $departmentIds)) {
                                    return false;
                                }
                            }
                            return true;
                        }
                    )
                ),
                "errors" => array (
                    "anonymous" => "发起部门id格式错误",
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