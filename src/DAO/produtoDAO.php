<?php

namespace Src\DAO;
use Src\config\Conexao;
use PDO;

class ProdutoDAO {
    public static function salvar($id, $nome, $tipo, $valor) {
        $pdo = Conexao::getInstance()->getPdo();

        // Verifica se ID já existe
        $verifica = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE id = ?");
        $verifica->execute([$id]);
        if ($verifica->fetchColumn() > 0) {
            return false; // Produto já existe
        }

        // Insere no banco
        $sql = "INSERT INTO produtos (id, nome, tipo, valor) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id, $nome, $tipo, $valor]);
    }
}