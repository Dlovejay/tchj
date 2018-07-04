<?php
/**
 * Created by PhpStorm.
 * User: taojin
 * Date: 2018/6/27
 * Time: 下午9:15
 */

class TaskModel extends CI_Model
{
    protected $table = 'task';

    private static $config = array(
        "0" => array(
            "DELETE" => array(
                "permissionType" => 1,//2表示部门有权限，1表示自己
                "next" => "6",
            ),
        ),
        "1" => array(
            "RECEIVE" => array(
                "permissionType" => 2,//2表示部门有权限，1表示自己
                "next" => "2",
            ),
            "DELETE" => array(
                "permissionType" => 1,//2表示部门有权限，1表示自己
                "next" => "6",
            ),
        ),
        "2" => array(
            "REPLY" => array(
                "permissionType" => 2,//2表示部门有权限，1表示自己
                "next" => "3",
            ),
            "REPEAL" => array(
                "permissionType" => 1,//2表示部门有权限，1表示自己
                "next" => "7",
            ),
        ),
        "3" => array(
            "FINISHED" => array(
                "permissionType" => 1,//2表示部门有权限，1表示自己
                "next" => "4",
            ),
            "BACK" => array(
                "permissionType" => 1,//2表示部门有权限，1表示自己
                "next" => "5",
            ),
            "REPEAL" => array(
                "permissionType" => 1,//2表示部门有权限，1表示自己
                "next" => "7",
            ),
        ),
        "5" => array(
            "REPLY" => array(
                "permissionType" => 2,//2表示部门有权限，1表示自己
                "next" => "3",
            ),
            "REPEAL" => array(
                "permissionType" => 1,//2表示部门有权限，1表示自己
                "next" => "7",
            ),
        ),
        "4" => array(),
        "6" => array(),
        "7" => array(),
    );


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function getJoinWhere($user, $params = array()) {
        $join = " task AS t ";
        $where = " 1=1 ";
        $param = array();
        $time = time();
        if ($user["tid"] == USERM) {

        } else if ($user["tid"] == USERL) {
            $where = " !(t.status = 1 AND t.start_at>{$time} AND t.pid != {$user['pid']}) ";
        } else {
            $join = " task AS t LEFT JOIN task_department_relation  AS r ON t.mid=r.mid AND t.pid != {$user['pid']} ";
            $where = " !(t.status = 1 AND t.start_at>{$time} AND t.pid != {$user['pid']}) AND (t.pid = {$user['pid']} OR r.pid={$user['pid']} OR r.pid=0 )";
        }

        if (isset($params["status"]) && $params["status"] !=="" && in_array($params["status"], array(0,1,2,3,4,5,6,7))) {
            $status = (int)$params["status"];
            if ($status == 0) {
                $where = $where . " AND (t.status=1 AND t.start_at > {$time}) ";
            } else if ($status == 1) {
                $where = $where . " AND (t.status=1 AND t.start_at < {$time}) ";
            } else {
                $where = $where . " AND t.status={$status} ";
            }
        }

        if (!empty($params["is_timeout"])) {
            if ($params["is_timeout"] == "TRUE") {
                $where = $where . " AND (t.is_timeout=1 OR (t.status=1 AND t.end_at<{$time}))";
            } else {
                $where = $where . " AND !(t.is_timeout=1 OR (t.status=1 AND t.end_at<{$time}))";
            }
        }

        if (!empty($params["keywords"])) {
            $where = $where . " AND title LIKE ?";
            $param[] = "%{$params["keywords"]}%";

        }

        return array("join" => $join, "where" => $where, "params" => $param);
    }


    public function getPageResult($user, $params) {
        $field = array('t.mid', 't.title', 't.content', 't.start_at', 't.end_at', 't.create_at', 't.update_at', 't.create_user_id', 't.last_do_user_id', 't.count', 't.status', 't.annex', 't.is_timeout', 't.departments');
        $mysqlParams = $this->getJoinWhere($user, $params);
        $countSql = " SELECT COUNT(*) AS total FROM " . $mysqlParams["join"] . " WHERE " . $mysqlParams["where"];
        $sql = " SELECT " . implode(",", $field) . " FROM " . $mysqlParams["join"] . " WHERE " . $mysqlParams["where"] . " ORDER BY t.update_at DESC ";
        $limit = $this->getLimit(isset($params['page'])?$params['page']:1, isset($params['pagesize'])?$params['pagesize']:15);
        $sql = $sql . " LIMIT {$limit["start"]}, {$limit["size"]}";
        // var_dump($sql);exit;
        $result = $this->db->query($sql, $mysqlParams["params"])->result_array();
        $row = $this->db->query($countSql, $mysqlParams["params"])->row_array();
        foreach ($result as &$v) {
            $this->toUnstartStatus($v);
            $v["annex"] = json_decode($v["annex"], true);
            $v["is_timeout"] = $this->isTimeout($v);
        }

        return array(
            "pager" => array(
                "total" => $row["total"],
                "page" => $limit["page"],
                "pagesize" => $limit["size"],
            ),
            "list" => $result,
        );
    }


    public function getLimit($page, $size) {
        $page = (int)$page;
        $size = (int)$size;
        if ($page <= 0) {
            $page = 1;
        }

        if ($size <= 0) {
            $size = 15;
        }

        $start = ($page-1) * $size;
        return array('start' => $start, 'size' => $size, 'page' => $page);
    }


    public function Add($fieldParams) {
        $departments = $fieldParams["departments"];
        $time = time();
        $fieldParams["last_do_user_id"] = $fieldParams["create_user_id"];
        $fieldParams["create_at"] = $time;
        $fieldParams['update_at'] = $time;
        $fieldParams["replys"] = "[]";
        $fieldParams["annex"] = json_encode($fieldParams["annex"]);
        $fieldParams["departments"] = implode(",", $fieldParams["departments"]);

        $this->db->trans_start();
        $sql = $this->db->set($fieldParams)->get_compiled_insert('task');
        $this->db->query($sql);
        $id = $this->db->insert_id();
        foreach($departments as $pid) {
            $ra = array("mid" => $id, "pid" => $pid);
            $this->db->insert('task_department_relation' , $ra);
        }
        $this->db->trans_complete();
        return $id;
    }


    public function edit($id, $fieldParams) {
        $departments = $fieldParams["departments"];
        $time = time();
        $fieldParams['update_at'] = $time;
        $fieldParams["annex"] = json_encode($fieldParams["annex"]);
        $fieldParams["departments"] = implode(",", $fieldParams["departments"]);
        $this->db->trans_start();
        $sql = $this->db->set($fieldParams)->where("mid", $id)->get_compiled_update("task");
        $this->db->query($sql);
        $this->db->query("DELETE FROM task_department_relation WHERE mid=?", $id);
        foreach($departments as $pid) {
            $ra = array("mid" => $id, "pid" => $pid);
            $this->db->insert('task_department_relation' , $ra);
        }
        $this->db->trans_complete();

        return $this->db->trans_status();
    }


    public function getOneById($id) {
        $sql = "SELECT * FROM ".$this->table." WHERE mid= ?";
        return $this->db->query($sql, $id)->row_array();
    }

    public function getOperation($task, $user) {
        if ($user["tid"] == 1) {
            return array("success" => true, "do" => array());
        } elseif($user["tid"] == 2) {
            if ($task['status'] == 0 && $task["pid"] != $user["pid"]) {
                return array("success" => false, "do" => array());
            }

            $dos = array();
            foreach(self::$config[$task["status"]] as $k=>$do) {
                if($do["permissionType"] == 1 && $task["pid"] == $user["pid"]) {
                   $dos[] = $k;
                }
            }
        } else {
            $departments = explode(",", $task["departments"]);

            if ($task['status'] == 0 && $task["pid"] != $user["pid"]) {
                return array("success" => false, "do" => array());
            }

            if ($departments[0] != 0 && $task["pid"] != $user["pid"] && !in_array($user["pid"], $departments)) {
                return array("success" => false, "do" => array());
            }


            $dos = array();
            foreach(self::$config[$task["status"]] as $k=>$do) {
                if($do["permissionType"] == 1 && $task["pid"] == $user["pid"]) {
                    $dos[] = $k;
                } elseif($do["permissionType"] == 2 && ($departments[0] == 0 || in_array($user["pid"], $departments))) {
                    $dos[] = $k;
                }
            }
        }

        return array("success" => true, "do" => $dos);
    }

    public function toUnstartStatus(&$task) {
        if ($task["start_at"] > time() && $task["status"] == 1 ) {
            $task["status"] = 0;
        }
    }

    public function isTimeout($task) {
        $time = time();
        if ($task["is_timeout"] == 1 || (in_array($task["status"], array(1,2)) && $task["end_at"] < $time)) {
            return true;
        } else {
            return false;
        }
    }


    public function canEdit($task, $user) {
        if ($task['pid'] == $user['pid'] && in_array($task['status'], array(1,2))) {
            return true;
        } else {
            return false;
        }
    }


    public function next($user, $params, $task, $isTimeout) {
        $time = time();
        $result = $this->getOperation($task, $user);
        if (!$result["success"]) {
            return array("success" => false);
        }

        $dos = $result["do"];
        if (!in_array($params["do"], $dos)) {
            return array("success" => false);
        }
        $nextStatus = self::$config[$task["status"]][$params["do"]]["next"];

        $replys = json_decode($task["replys"], true);
        $reply = array(
            'reply_id' => count($replys) + 1,
            "create_user_id" => $user["uid"],
            "content" => $params["content"],
            "update_at" => time(),
            "status" => $nextStatus,
        );

        $replys[] = $reply;

        $fields = array (
            "update_at" => $time,
            "last_do_user_id" => $user["uid"],
            "status" => $nextStatus,
            "count" => in_array($params["do"], array("RECEIVE", "REPLY", "FINISHED", "BACK"))?$task["count"]+1:$task['count'],
            "is_timeout" => $isTimeout,
            "replys" => json_encode($replys),
        );

        if (isset($params["cause"])) {
            $fields["cause"] = $params["cause"];
        }


        $sql = $this->db->set($fields)->where("mid", $params["id"])->get_compiled_update("task");
        $this->db->query($sql);
        return array("success" => true);
    }


    public function GetStatistics($user) {
        //array("join" => $join, "where" => $where, "params" => $param);
        $field = array('t.mid', 't.start_at', 't.end_at', 't.count', 't.status', 't.is_timeout', 't.departments', 't.pid');
        $mysqlParams = $this->getJoinWhere($user, array());
        $sql = " SELECT " . implode(",", $field) . " FROM " . $mysqlParams["join"] . " WHERE " . $mysqlParams["where"] . " AND t.`status` != 6 ";
        $result = $this->db->query($sql, $mysqlParams["params"])->result_array();
        foreach($result as &$v) {
            $v['timeout'] = $this->isTimeout($v);
            $v['departmentIds'] = explode(",", $v['departments']);
        }

        if ($user['tid'] == 1 || $user['tid'] == 2) {
            $data = array('total' => array('pid' => 0, 'name' => '总计', 'count' => $this->count($result, 0)));
            $CI =& get_instance();
            $CI->load->model('Department');
            $departments = $CI->Department->getListByLevels(array(1));
            foreach ($departments as $d) {
                $data[$d['pid']] = array('pid' => $d['pid'], 'name' => $d['pname'], 'count' => $this->count($result, $d['pid']));
            }
        } else {
            $data = array($user['pid'] => array('pid' => $user['pid'], 'name' => '总计', 'count' => $this->count($result, $user['pid'])));
        }
        

        return $data;
    }

    private function countInit() {
        return array(
            'total' => 0,
            'doing' => 0,
            'repeal' => 0,
            'finish' => 0,
            'first_finish' => 0,
            'timeout' => 0,
            'reply' => 0,
        );
    }

    private function count($tasks, $pid) {
        $count = $this->countInit();
        foreach ($tasks as $task) {
            if ($pid == 0 || $task['departments'] == '0' || $task['pid'] == $pid || in_array($pid, $task['departmentIds'])) {

                $count['total'] = $count['total'] + 1;

                if (in_array($task['status'], array(1, 2, 3, 5))) {
                    $count['doing'] = $count['doing'] + 1;
                }

                if ($task['status'] == 7) {
                    $count['repeal'] = $count['repeal'] + 1;
                }

                if ($task['status'] == 4) {
                    $count['finish'] = $count['finish'] + 1;
                    if ($task['count'] == 3) {
                        $count['first_finish'] = $count['first_finish'] + 1;
                    }
                }

                if ($task['timeout']) {
                    $count['timeout'] = $count['timeout'] + 1;
                }

                if ($task['status'] == 3) {
                    $count['reply'] = $count['reply'] + 1;
                }
            }
        }
        //var_dump($count);exit;
        $count['first_finish_percent'] = $count['finish'] == 0?0:round($count['first_finish']/$count['finish']*100, 1);
        $count['reply_percent'] = $count['doing'] == 0?0:round($count['reply']/$count['doing']*100, 1);
        unset($count['first_finish']);
        unset($count['reply']);
        unset($count['finish']);
        return $count;
    }


}