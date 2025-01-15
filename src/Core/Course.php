<?php

namespace Src\Core;

use Src\Base\Log;
use Src\System\DatabaseMethods;

class Course
{

    private $dm = null;
    private $log = null;

    public function __construct($db, $user, $pass)
    {
        $this->dm = new DatabaseMethods($db, $user, $pass);
        $this->log = new Log($db, $user, $pass);
    }

    public function fetch(string $key = "", string $value = "", bool $archived = false)
    {
        switch ($key) {
            case 'code':
                $concat_stmt = "AND c.`code` = :v";
                break;

            case 'name':
                $concat_stmt = "AND c.`name` = :v";
                break;

            case 'category':
                $concat_stmt = "AND c.`fk_category` = :v";
                break;

            case 'department':
                $concat_stmt = "AND c.`fk_department` = :v";
                break;

            default:
                $concat_stmt = "";
                break;
        }

        $query = "SELECT `code`, c.`name`, c.`credit_hours`, c.`contact_hours`, c.`semester`, c.`level`, c.`archived`, 
                c.`fk_category`, c.`fk_department`, cg.`name` AS category, d.`name` AS `department` 
                FROM `courses` AS c, `category` AS cg, `department` AS d 
                WHERE c.`fk_category` = cg.`id` AND c.`fk_department` = d.`id` AND c.`archived` = :ar $concat_stmt";
        $params = $value ? array(":v" => $value, ":ar" => $archived) : array();
        return $this->dm->getData($query, $params);
    }

    public function add(array $data)
    {
        $query = "INSERT INTO courses (`code`, `name`, `credit_hours`, `contact_hours`, `semester`, `level`, 
                `fk_category`, `fk_department`, `archived`) 
                VALUES(:c, :n, :ch, :th, :s, :l, :cg, :dm, :ar)";
        $params = array(
            ":c" => $data["code"],
            ":n" => $data["name"],
            ":ch" => $data["credit_hours"],
            ":th" => $data["contact_hours"],
            ":s" => $data["semester"],
            ":l" => $data["level"],
            ":cg" => $data["category"],
            ":dm" => $data["department"],
            ":ar" => 0
        );
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result) $this->log->activity($_SESSION["user"], "INSERT", "Added new course {$data["name"]}");
        return $query_result;
    }

    public function update(array $data)
    {
        $query = "UPDATE courses SET 
        `code`=:c, `name`=:n, `credit_hours`=:ch, `contact_hours`=:th, `semester`=:s, `level`=:l, 
        `fk_category`=:cg, `fk_department`=:dm, `archived`=:ar WHERE `code` = :c";
        $params = array(
            ":c" => $data["code"],
            ":n" => $data["name"],
            ":ch" => $data["credit_hours"],
            ":th" => $data["contact_hours"],
            ":s" => $data["semester"],
            ":l" => $data["level"],
            ":cg" => $data["category"],
            ":dm" => $data["department"],
            ":ar" => 0
        );
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result) $this->log->activity($_SESSION["user"], "UPDATE", "Updated information for course {$data["code"]}");
        return $query_result;
    }

    public function archive($code)
    {
        $query = "UPDATE courses SET archived = 1 WHERE `code` = :c";
        $query_result = $this->dm->inputData($query, array(":c" => $code));
        if ($query_result) $this->log->activity($_SESSION["user"], "DELETE", "Archived course {$code}");
        return $query_result;
    }

    public function delete($code)
    {
        $query = "DELETE FROM courses WHERE code = :c";
        $query_result = $this->dm->inputData($query, array(":c" => $code));
        if ($query_result) $this->log->activity($_SESSION["user"], "DELETE", "Deleted courseme {$code}");
        return $query_result;
    }

    public function total(string $key = "", string $value = "", bool $archived = false)
    {
        switch ($key) {
            case 'category':
                $concat_stmt = "AND c.`fk_category` = :v";
                break;

            case 'department':
                $concat_stmt = "AND c.`fk_department` = :v";
                break;

            default:
                $concat_stmt = "";
                break;
        }
        $query = "SELECT COUNT(c.`code`) FROM `courses` AS c, `category` AS cg, `department` AS d 
        WHERE c.`fk_category` = cg.`id` AND c.`fk_department` = d.`id` AND c.`archived` = :ar $concat_stmt";
        $params = $value ? array(":v" => $value, ":ar" => $archived) : array();
        return $this->dm->getData($query, $params);
    }
}