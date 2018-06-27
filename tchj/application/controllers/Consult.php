<?php
class Consult extends MY_Controller
{
    public function __construct(){
        parent::__construct();
    }

    public function index()
    {

        $data = array(
            'base_url' => $this->config->item('base_url')
        );

        //加载用户model
        $this->load->model('ConsultList');
        $data['total'] = $this->ConsultList->query_total();

//        $this->load->library('pagination');
//        $config['base_url'] = base_url('consult/index');
//        $config['total_rows'] = $data['total'];
//        $config['per_page'] = $this->limit;
//        $this->pagination->initialize($config);

//        $offset =  $this->uri->segment(3);
//        if (empty($offset)) {
//            $offset = 0;
//        }
//
//        $where = [];
//        $field = '*';
//        $result = $this->ConsultList->query($where, $field, $this->limit, $offset);
//        $data['result'] = $result;
        $this->load->view('consult/index.php',$data);
    }

    # 请示列表
    public function consult_list()
    {
        $create_uid = intval($this->input->get('create_uid'));
        $status = intval($this->input->get('status'));
        $start_date = trim($this->input->get('start_date'));
        $end_date = trim($this->input->get('end_date'));
        $page = intval($this->input->get('page'));
        $page_size = intval($this->input->get('page_size'));

        # 就不做校验了  对于不合法的数据 当成没有这个条件
        $where = ' 1 = 1 ';
        if(!empty($create_uid)){
            $where .= ' AND uid = ' . $create_uid;
        }
        if(!empty($status)){
            $where .= ' AND check_status = ' . $status;
        }
        if(!empty($start_date) && strtotime($start_date)){
            $where .= ' AND create_date >= "' . $start_date . '"';
        }
        if(!empty($end_date) && strtotime($end_date)){
            $where .= ' AND create_date <= "' . $end_date . '"';
        }
        if(!empty($page)){
            $page = 1;
        }
        if(!empty($page_size)){
            $page_size = 10;
        }
        $offset = ($page - 1) * $page_size;
        $field = '*';
        $this->load->model('ConsultList');
        $result = $this->ConsultList->query($where, $field, $page_size, $offset);

        $return = array(
            'code' => 0,
            'message' => '',
            'data' => $result
        );

        echo json_encode($return);
        exit();
    }

    # 添加请示
    public function add_consult()
    {
        # TODO 还得加上权限判断
        $title = trim($this->input->get('title'));
        $content = trim($this->input->get('content'));
        $annex = trim($this->input->get('annex'));

        $return = array(
            'code' => 0,
            'message' => '',
            'data' => []
        );
        if(empty($title) || empty($content) || empty($annex)){
            $return['code'] = 10;
            $return['message'] = '参数不能为空';

            echo json_encode($return);
            exit();
        }
        $user_info = $this->session->userdata('user_info');

        $this->load->model('ConsultList');
        $data = array(
            'title' => $title,
            'content' => $content,
            'annex' => $annex,
            'uid' => $user_info['uid']
        );

        if($this->db->insert('consultlist', $data)){
            $id = $this->db->insert_id();
            $return['data'] = $id;
        }else{
            $return['code'] = 11;
            $return['message'] = '发表请示失败,请重试';
        }

        echo json_encode($return);
        exit();
    }

    # 修改请示
    public function edit_consult()
    {
        # TODO 还得加上权限判断

        $id = intval($this->input->get('id'));
        $title = trim($this->input->get('title'));
        $content = trim($this->input->get('content'));
        $annex = trim($this->input->get('annex'));

        $return = array(
            'code' => 0,
            'message' => '',
            'data' => []
        );
        if(empty($title) || empty($content) || empty($annex)){
            $return['code'] = 10;
            $return['message'] = '参数不能为空';

            echo json_encode($return);
            exit();
        }
        $this->load->model('ConsultList');
        $user_info = $this->session->userdata('user_info');

        $consolt_info = $this->ConsultList->query('id = ' . $id);
        if(empty($consolt_info) || $consolt_info[0]['uid'] != $user_info['uid']){
            $return['code'] = 12;
            $return['message'] = '您没有权限修改他人的请示';

            echo json_encode($return);
            exit();
        }

        # TODO 请示有哪些状态下允许修改

        $data = array(
            'title' => $title,
            'content' => $content,
            'annex' => $annex
        );

        $this->db->where('id', $id);
        if(!$this->db->update('consultlist', $data)){
            $return['code'] = 11;
            $return['message'] = '修改请示失败,请重试';
        }

        echo json_encode($return);
        exit();
    }


}