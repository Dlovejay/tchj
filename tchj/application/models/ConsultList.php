<?php
class ConsultList extends CI_Model
{
    public $table = 'consultlist';
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function query($where=array(), $field="*", $limit=10, $offset=0)
    {
        $this->db->select($field)->from($this->table)->where($where)->limit($limit, $offset);
        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function query_total($where=array())
    {
        if (empty($where)) {
            return $this->db->count_all($this->table);
        }else{
            return $this->db->where($where)->count_all_results($this->table);
        }
    }
}

?>