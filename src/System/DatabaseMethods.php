<?php

namespace Src\System;

use Exception;
use PDOException;
use Src\System\DatabaseConnector;

class DatabaseMethods
{
    private $conn = null;
    private $query = "";
    private $params = [];
    private $stmt = null;
    private $inTransaction = false;

    public function __construct($db, $user, $pass)
    {
        $this->conn = (new DatabaseConnector($db, $user, $pass))->connect();
    }

    // New transaction methods
    public function beginTransaction()
    {
        if (!$this->inTransaction) {
            try {
                $this->inTransaction = $this->conn->beginTransaction();
                return $this->inTransaction;
            } catch (PDOException $e) {
                exit(json_encode(array("error" => "Transaction start failed: " . $e->getMessage())));
            }
        }
        return false;
    }

    public function commit()
    {
        if ($this->inTransaction) {
            try {
                $this->conn->commit();
                $this->inTransaction = false;
                return true;
            } catch (PDOException $e) {
                exit(json_encode(array("error" => "Transaction commit failed: " . $e->getMessage())));
            }
        }
        return false;
    }

    public function rollback()
    {
        if ($this->inTransaction) {
            try {
                $this->conn->rollBack();
                $this->inTransaction = false;
                return true;
            } catch (PDOException $e) {
                exit(json_encode(array("error" => "Transaction rollback failed: " . $e->getMessage())));
            }
        }
        return false;
    }

    private function query($str, $params = array())
    {
        try {
            $this->query = $str;
            $stmt = $this->conn->prepare($str);
            $stmt->execute($params);

            if (explode(' ', $str)[0] == 'SELECT' || explode(' ', $str)[0] == 'CALL') {
                return $stmt->fetchAll();
            } elseif (explode(' ', $str)[0] == 'INSERT' || explode(' ', $str)[0] == 'UPDATE' || explode(' ', $str)[0] == 'DELETE') {
                return 1;
            }
        } catch (PDOException $e) {
            if ($this->inTransaction) {
                $this->rollback();
            }
            exit(json_encode(array("error" => $e->getMessage())));
        }
    }

    final public function getID($str, $params = array())
    {
        try {
            $result = $this->query($str, $params);
            if (!empty($result)) return $result[0]["id"];
            return 0;
        } catch (Exception $e) {
            if ($this->inTransaction) {
                $this->rollback();
            }
            echo $e->getMessage();
        }
    }

    final public function getData($str, $params = array())
    {
        try {
            $result = $this->query($str, $params);
            if (!empty($result)) return $result;
            return 0;
        } catch (Exception $e) {
            if ($this->inTransaction) {
                $this->rollback();
            }
            echo $e->getMessage();
        }
    }

    final public function inputData($str, $params = array())
    {
        try {
            $result = $this->query($str, $params);
            if (!empty($result)) return $result;
            return 0;
        } catch (Exception $e) {
            if ($this->inTransaction) {
                $this->rollback();
            }
            echo $e->getMessage();
        }
    }

    public function run($query, $params = [])
    {
        $this->query = $query;
        $this->params = $params;
        try {
            $this->stmt = $this->conn->prepare($this->query);
            $this->stmt->execute($this->params);
            return $this->stmt;
        } catch (PDOException $e) {
            if ($this->inTransaction) {
                $this->rollback();
            }
            exit(json_encode(array("error" => $e->getMessage())));
        }
    }

    private function type(): mixed
    {
        return explode(' ', $this->query)[0];
    }

    public function all()
    {
        if ($this->type() == 'SELECT') return $this->stmt->fetchAll();
    }

    public function one()
    {
        if ($this->type() == 'SELECT') return $this->stmt->fetch();
    }

    public function add($autoIncrementColumn = null, $primaryKeyValue = null)
    {
        if ($this->type() == 'INSERT') {
            if ($autoIncrementColumn) return $this->conn->lastInsertId($autoIncrementColumn);
            else if ($primaryKeyValue) return $primaryKeyValue;
            else return true;
        }
        return false;
    }

    public function remove()
    {
        if ($this->type() == 'DELETE') return $this->stmt->rowCount();
        return false;
    }

    public function edit()
    {
        if ($this->type() == 'UPDATE') return $this->stmt->rowCount();
        return false;
    }
}
