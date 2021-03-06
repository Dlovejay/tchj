<?php
class Consult extends MY_Controller
{
    public function __construct(){
        parent::__construct();
    }

    public function index()
    {

        /*$data = array(
            'base_url' => $this->config->item('base_url')
        );

        # 发送请示指向的部门
        $this->load->model('ConsultList');
        $where = ['plevel' => 1];
        $departments = $this->db->where($where)->select('pid, pname')->get('department', 0, 0);
        $data['departments'] = $departments->result_array();
				*/
				$this->load->model('User');
        $result=$this->User->user();
        $data['user']=$result['data'];
        $data['account']=$_SESSION['user'];
        $this->load->model('Base');
        $result=$this->Base->department();
        $data['department']=$result['data'];
        # 部门,用户数据
        //$this->load->view('consult/index.php',$data);
				$this->load->view('consult/consultlist.php',$data);
    }

    # 请示列表
    public function consult_list()
    {
        $create_uid = intval($this->input->post('create_uid'));
        //$status = intval($this->input->post('status'));
        $status = $this->input->post('status');
				$start_date = trim($this->input->post('start_date'));
        $end_date = trim($this->input->post('end_date'));
				$keywords = trim($this->input->post('keywords'));
        $page = intval($this->input->post('page'));
        $page_size = intval($this->input->post('page_size'));

        $user_info = $this->session->userdata('user');
        # 就不做校验了  对于不合法的数据 当成没有这个条件
        # 还需要看用户是什么级别,如果不是领导 那就只能看自己发布的请示
        if($user_info['tid'] == USERD){
            $where = ' c.created_pid = ' . $user_info['pid'];
        }else if($user_info['tid'] == USERU){
            $where = ' c.pid = ' . $user_info['pid'];
        }else{
						$where = ' 1=1';
				}

        if(!empty($create_uid)){
            $where .= ' AND c.uid = ' . $create_uid;
        }
        //if(!empty($status)){
				if ($status!==''){
					$status=intval($status);
            //$where .= ' AND c.check_status = ' . $status;
					$where .= ' AND c.status = ' . $status;
				}
        if(!empty($start_date) && strtotime($start_date)){
            $where .= ' AND c.create_date >= "' . $start_date . '"';
        }
        if(!empty($end_date) && strtotime($end_date)){
            $where .= ' AND c.create_date <= "' . $end_date . '"';
        }
				if ($keywords!==''){
					$where .=' AND title LIKE \'%'. $keywords .'%\'';
				}


        if(empty($page)){
            $page = 1;
        }
        if(empty($page_size)){
            $page_size = 10;
        }
        $offset = ($page - 1) * $page_size;
        $field = 'c.*,u.username';
        $this->load->model('ConsultList');
        $result = $this->ConsultList->query($where, $field, $page_size, $offset);
				$allcount=$this->ConsultList->query_total($where);
        $return = array(
            'code' => 0,
            'message' => '',
            'data' => array(
							'total'=>$allcount,
							'page'=>$page,
							'list'=>$result
						)
        );

        echo json_encode($return);
        exit();
    }

    # 处理添加请示
    public function add_consult()
    {
        $title = trim($this->input->post('title'));
        $content = trim($this->input->post('content'));
        $annex = trim($this->input->post('annex'));
        $pid = trim($this->input->post('pid'));

        $return = rexGetMReturn();

        $user_info = $this->session->userdata('user');
        # 只有普通用户才能发布请示
        if($user_info['tid'] != USERD){
            $return['code'] = 401;
            $return['message'] = '您无权发布请示';

            echo json_encode($return);
            exit();
        }

        if(empty($title) || empty($content)){
            $return['code'] = 401;
            $return['message'] = '参数不能为空';

            echo json_encode($return);
            exit();
        }

        $this->load->model('ConsultList');
        $data = array(
            'title' => $title,
            'content' => $content,
            'annex' => $annex,
            'uid' => $user_info['uid'],
            'status' => 0,
            'pid' => $pid,
            'created_pid' => $user_info['pid']
        );

        if($this->db->insert('consultlist', $data)){
            $id = $this->db->insert_id();
            $return['data'] = $id;
        }else{
            $return['code'] = 401;
            $return['message'] = '发表请示失败,请重试';
        }

        echo json_encode($return);
        exit();
    }

    # 修改请示
    public function edit_consult()
    {
        $id = intval($this->input->post('id'));
        $title = trim($this->input->post('title'));
        $content = trim($this->input->post('content'));
        $annex = trim($this->input->post('annex'));
        $pid = trim($this->input->post('pid'));

        $return = rexGetMReturn();

        if(empty($title) || empty($content)){
            $return['code'] = 401;
            $return['message'] = '参数不能为空';

            echo json_encode($return);
            exit();
        }
        $this->load->model('ConsultList');
        $user_info = $this->session->userdata('user');

        $consolt_info =  $this->db->where('id', $id)->get('consultlist')->result_array();
        if(empty($consolt_info) || $consolt_info[0]['uid'] != $user_info['uid']){
            $return['code'] = 401;
            $return['message'] = '您没有权限修改他人的请示';

            echo json_encode($return);
            exit();
        }

        if($consolt_info[0]['status'] != 0){
            $return['code'] = 401;
            $return['message'] = '请示已被接收,无法修改';

            echo json_encode($return);
            exit();
        }

        $data = array(
            'title' => $title,
            'content' => $content,
            'annex' => $annex,
            'pid' => $pid
        );

        $this->db->where('id', $id);
        if(!$this->db->update('consultlist', $data)){
            $return['code'] = 401;
            $return['message'] = '修改请示失败,请重试';
        }

        echo json_encode($return);
        exit();
    }


    # 回复请示
    public function replies_consult()
    {
        $cid = intval($this->input->post('cid'));
        $content = trim($this->input->post('content'));
        $complete = intval($this->input->post('complete'));

        $return = rexGetMReturn();

        if(empty($cid) || empty($content)){
            $return['code'] = 401;
            $return['message'] = '参数不完整';

            echo json_encode($return);
            exit();
        }
        $user_info = $this->session->userdata('user');

        $this->load->model('ConsultList');

        # 查看该请示是否存在
        $consult_info = $this->db->where('id', $cid)->get('consultlist')->result_array();
        if(empty($consult_info)){
            $return['code'] = 401;
            $return['message'] = '该请示不存在';

            echo json_encode($return);
            exit();
        }
        # 有些状态是不可以回复的
        if(!in_array($consult_info[0]['status'], [0, 1])){
            $return['code'] = 401;
            $return['message'] = '已完成或已撤销的请示无法回复';

            echo json_encode($return);
            exit();
        }

        $data = array(
            'cid' => $cid,
            'content' => $content,
            'uid' => $user_info['uid'],
            'retype' => 0
        );

        if($this->db->insert('consultreturn', $data)){
            $id = $this->db->insert_id();
            $return['data'] = $id;
            # 请示状态:未接受 1待处理 2处理完成 3撤销
            $status = 1;
            if($complete == 1){
                $status = 2;
            }
            # 请示批示人
            $act_user = 0;
            if($user_info['tid'] != USERD){
                $act_user = $user_info['uid'];
            }
            # 请示插入成功 将consultlist表的rcount字段+1
            $this->db->where('id', $cid);
            $this->db->set('rcount', 'rcount + 1', false);
            $this->db->set('status', $status, false);
            $this->db->set('act_user', $act_user, false);
            $this->db->update('consultlist');
        }else{
						die($this->db->last_query());
            $return['code'] = 401;
            $return['message'] = '发表请示失败,请重试';
        }

        echo json_encode($return);
        exit();
    }

    # 删除请示
    public function del_consult()
    {
        $cid = intval($this->input->post('cid'));
        $user_info = $this->session->userdata('user');

        # 随便载入一个model
        $this->load->model('ConsultList');
        $consult_info = $this->db->where('id', $cid)->get('consultlist')->result_array();

        $return = rexGetMReturn();
        if(empty($consult_info)){
            $return['code'] = 401;
            $return['message'] = '该请示不存在';

            echo json_encode($return);
            exit();
        }
        if($consult_info[0]['uid'] != $user_info['uid']){
            $return['code'] = 401;
            $return['message'] = '不能操作不是自己发布的请示';

            echo json_encode($return);
            exit();
        }

        # status = 0的请示可以删除,其余状态的只能撤销
        if($consult_info[0]['status'] == 0){
            $this->db->where('id', $cid)->delete('consultlist');
        }else{
            $this->db->where('id', $cid);
            $this->db->set('status', 3, FALSE);
            $this->db->update('consultlist');
						$data = array(
								'cid' => $cid,
								'content' => '请示已被撤销',
								'uid' => $user_info['uid'],
								'retype' => 0
						);
						if ($this->db->insert('consultreturn', $data)){
						}else{
								$return['code']=500;
								$return['message']='插入撤销信息错误，请管理员核查';
								echo json_encode($return);
								exit();
						}
        }

        echo json_encode($return);
        exit();
    }

    # 请示回复列表
    public function consult_replies()
    {
        $cid = intval($this->input->post('cid'));
        $page = intval($this->input->post('page'));
        $page_size = intval($this->input->post('page_size'));
        $user_info = $this->session->userdata('user');

        $return = rexGetMReturn();
        if(empty($cid)){
            $return['code'] = 401;
            $return['message'] = '请示id不能为空';

            echo json_encode($return);
            exit();
        }

        # 随便载入一个model
        $this->load->model('ConsultList');
        $consult_info = $this->db->where('id', $cid)->get('consultlist')->result_array();

        $return = rexGetMReturn();
        if(empty($consult_info)){
            $return['code'] = 401;
            $return['message'] = '该请示不存在';

            echo json_encode($return);
            exit();
        }
				$nowstatus=$consult_info[0]['status'];

        # 如果是普通用户 则只能看到自己发布的请示
        if($user_info['tid'] == USERD && $consult_info[0]['uid'] != $user_info['uid']){
            $return['code'] = 401;
            $return['message'] = '这不是您发布的请示!';

            echo json_encode($return);
            exit();
        }
        $where = ['cid' => $cid];
        if(empty($page)){
            $page = 1;
        }
        if(empty($page_size)){
            $page_size = 10;
        }
        $offset = ($page - 1) * $page_size;
        $result = $this->ConsultList->query($where, '*', $page_size, $offset, 'consultreturn c');

        $return['data'] = array(
					'list'=>$result,
					'status'=>$nowstatus
				);

        echo json_encode($return);
        exit();
    }

    public function consult_statistics()
    {
        $user_info = $this->session->userdata('user');
        $this->load->model('ConsultList');

        $consult_info = $this->ConsultList->consult_statistics($user_info['pid']);

        $return = rexGetMReturn();
        $return['data'] = $consult_info;
        echo json_encode($return);
        exit;
    }
		
		public function statistics(){
				$this->load->model('Base');
				$temp=$this->Base->department();
				$departments=$temp['data'];
        $user = $_SESSION['user'];
        $this->load->model('ConsultList');
        rexAjaxReturn(0, '', $this->ConsultList->GetStatistics($user,$departments));
    }
}