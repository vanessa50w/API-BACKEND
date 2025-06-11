<?php

class Conexao {
    private static $host = 'localhost';
    private static $dbname = 'loja';
    private static $username = 'root';
    private static $password = '';
    private static $pdo = null;

    // Construtor privado - impede new Conexao()
    private function __construct() {}

    // Impede clonagem
    private function __clone() {}

    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8",
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                throw new Exception("Erro na conexão: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}