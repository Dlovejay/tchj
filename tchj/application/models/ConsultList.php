<?php
class ConsultList extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function query($where=array(), $field="*", $limit=10, $offset=0, $table='consultlist')
    {
        $this->db->select($field)->where($where);
        $query = $this->db->get($table, $offset, $limit);
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