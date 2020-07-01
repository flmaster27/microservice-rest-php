<?php

namespace App\DB;

class Connector
{

    private $dbConnection = null;

    public function __construct()
    {
        $host = getenv('MYSQL_HOST');
        $port = getenv('MYSQL_PORT');
        $db   = getenv('MYSQL_DATABASE');
        $user = getenv('MYSQL_USER');
        $pass = getenv('MYSQL_PASSWORD');

        try {
            $this->dbConnection = new \PDO(
                "mysql:host=$host;port=$port;dbname=$db",
                $user,
                $pass
            );
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }
}