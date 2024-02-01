<?php

namespace Src\System;

use Exception;
use Src\System\DatabaseConnector;

class DatabaseMethods
{
    private $conn;
    private $stmt;
    private $query;

    function __construct($db, $user, $pass)
    {
        $this->conn = (new DatabaseConnector($db, $user, $pass))->connect();
    }

    private function query($str, $params = array())
    {
        $this->query = $str;
        $stmt = $this->conn->prepare($str);
        $stmt->execute($params);
        if (explode(' ', $str)[0] == 'SELECT' || explode(' ', $str)[0] == 'CALL') {
            return $stmt->fetchAll();
        } elseif (explode(' ', $str)[0] == 'INSERT' || explode(' ', $str)[0] == 'UPDATE' || explode(' ', $str)[0] == 'DELETE') {
            return 1;
        }
    }

    public function run($query, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $this->stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function all()
    {
        if (explode(' ', $this->query)[0] == 'SELECT') return $this->stmt->fetchAll();
    }

    public function one()
    {
        if (explode(' ', $this->query)[0] == 'SELECT') return $this->stmt->fetch();
    }

    public function add($autoIncrementColumn = null, $primaryKeyValue = null)
    {
        if (explode(' ', $this->query)[0] == 'INSERT') {
            if ($autoIncrementColumn) return $this->conn->lastInsertId($autoIncrementColumn);
            else if ($primaryKeyValue !== null) return $primaryKeyValue;
            else return true;
        }
        return false;
    }

    public function remove()
    {
        if (explode(' ', $this->query)[0] == 'DELETE') return $this->stmt->rowCount();
        return false;
    }

    public function edit()
    {
        if (explode(' ', $this->query)[0] == 'UPDATE') return $this->stmt->rowCount();
        return false;
    }

    //Get raw data from db
    final public function getID($str, $params = array())
    {
        try {
            $result = $this->query($str, $params);
            if (!empty($result))  return $result[0]["id"];
            return 0;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    //Get raw data from db
    final public function getData($str, $params = array())
    {
        try {
            $result = $this->query($str, $params);
            if (!empty($result)) return $result;
            return 0;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    //Insert, Upadate or Delete Data
    final public function inputData($str, $params = array())
    {
        try {
            $result = $this->query($str, $params);
            if (!empty($result)) return $result;
            return 0;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
