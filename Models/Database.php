<?php
namespace Models;

class Database
{
    private $pdo;

    public function __construct()
    {
        // $dsn      = 'mysql:host=127.0.0.1;port=3306;dbname=casteria;charset=utf8';
        $dsn = 'mysql:host=localhost;dbname=casteria;charset=utf8';
        $user     = 'root';
        $password = 'root';
        $opt      = [
            // エラーを取得するため
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            // sqlインジェクションを回避するため
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];
    
        $this->pdo = new \PDO($dsn, $user, $password, $opt);
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}