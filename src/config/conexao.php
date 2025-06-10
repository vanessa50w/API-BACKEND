<?php

namespace Src\config;

use PDO;

class Conexao { 
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $this->pdo = new PDO('mysql:host=localhost;dbname=loja', 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Conexao();
        }
        return self::$instance;
    }

    public function getPdo() {
        return $this->pdo;
    }
}
