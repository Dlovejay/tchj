<?php
/**
 * Created by PhpStorm.
 * User: taojin
 * Date: 2018/6/27
 * Time: 下午9:50
 */

class Department extends CI_Model
{
    public $table = 'department';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getSubordinates() {

    }


    public function getListByLevels($levels = array()) {
        $sql = "SELECT * FROM ". $this->table;
        if (count($levels) > 0) {
            $str = implode(",", $levels);
            $sql = $sql . " WHERE plevel IN({$str})";
        }
        return $this->db->query($sql)->result_array();
    }


}