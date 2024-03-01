<?php

namespace Root\App\Services;

use Exception;
use PDO;

class Database
{
    protected string $host;
    protected string $database;
    protected string $username;
    protected string $password;
    protected PDO $connection;
    
    static protected ?Database $app = null;
    
    /**
     * @throws Exception
     */
    public function __construct()
    {
        try {
            $this->host = getenv('DB_HOST');
            $this->database = getenv('DB_NAME');
            $this->username = getenv('DB_USERNAME');
            $this->password = getenv('DB_PASSWORD');
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database}",
                $this->username,
                $this->password
            );
        } catch (\PDOException $e) {
            // throw new Exception($e->getMessage());
            echo '<pre>';
            print_r($e->getMessage());
            echo '</pre>';
        }
    }
    
    static public function app(): PDO
    {
        if (!static::$app) {
            static::$app = new static();
        }
        return static::$app->connection;
    }
}