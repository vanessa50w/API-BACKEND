<?php

namespace Src\DAO;

use Src\Model\Produto;
use Conexao;
use PDO;
use Exception;

class ProdutoDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexao::getConnection();
    }

    public function criar(Produto $produto) {
        try {
            $sql = "INSERT INTO produtos (id, nome, valor, tipo) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $produto->getId(),
                $produto->getNome(),
                $produto->getValor(),
                $produto->getTipo()
            ]);
            
            return $produto;
        } catch (Exception $e) {
            throw new Exception("Erro ao criar produto: " . $e->getMessage());
        }
    }

    public function buscarTodos() {
        try {
            $sql = "SELECT * FROM produtos";
            $stmt = $this->pdo->query($sql);
            $produtos = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $produto = new Produto($row['id'], $row['nome'], $row['valor'], $row['tipo'] ?? null);
                $produto->setId($row['id']);
                $produto->setNome($row['nome']);
                $produto->setValor($row['valor']);
                $produto->setTipo($row['tipo']);
                $produtos[] = $produto;
            }
            
            return $produtos;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar produtos: " . $e->getMessage());
        }
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT * FROM produtos WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $produto = new Produto($row['id'], $row['nome'], $row['valor'], $row['tipo'] ?? null);
                $produto->setId($row['id']);
                $produto->setNome($row['nome']);
                $produto->setValor($row['valor']);
                $produto->setTipo($row['tipo']);
                return $produto;
            }
            
            return null;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar produto: " . $e->getMessage());
        }
    }

    public function atualizar(Produto $produto) {
        try {
            $sql = "UPDATE produtos SET nome = ?, valor = ?, tipo = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $produto->getNome(),
                $produto->getValor(),
                $produto->getTipo(),
                $produto->getId()
            ]);
            
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar produto: " . $e->getMessage());
        }
    }

    public function deletar($id) {
        try {
            $sql = "DELETE FROM produtos WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$id]);
            
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar produto: " . $e->getMessage());
        }
    }
}