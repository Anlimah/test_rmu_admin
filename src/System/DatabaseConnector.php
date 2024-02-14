<?php

namespace Src\System;

use PDO;
use PDOException;

class DatabaseConnector
{
    private $conn = null;

    public function __construct($db, $user, $pass)
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db";
        try {
            $this->conn = new PDO($dsn, $user, $pass, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            exit(json_encode(array("error" => $e->getMessage())));
        }
    }

    public function connect()
    {
        return $this->conn;
    }
}
