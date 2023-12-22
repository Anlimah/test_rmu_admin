<?php

namespace Src\System;

class DatabaseConnector
{

    private $conn = null;

    public function __construct($db, $user, $pass)
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');

        try {
            $this->conn = new \PDO("mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db", $user, $pass);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
