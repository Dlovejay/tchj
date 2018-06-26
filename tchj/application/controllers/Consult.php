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

        $this->load->library('pagination');
        $config['base_url'] = base_url('consult/index');
        $config['total_rows'] = $data['total'];
        $config['per_page'] = $this->limit;
        $this->pagination->initialize($config);

        $offset =  $this->uri->segment(3);
        if (empty($offset)) {
            $offset = 0;
        }

        $where = [];
        $field = '*';
        $result = $this->ConsultList->query($where, $field, $this->limit, $offset);
        $data['result'] = $result;
        $this->load->view('consult/index.php',$data);
    }
}