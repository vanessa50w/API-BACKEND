<?php

namespace Src\DAO;

use Src\Config\Conexao;
use PDO;
use Exception;

class CompraDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function criar($id, $valorEntrada, $qtdParcelas, $idProduto)
    {
        try {
            $sql = "INSERT INTO compras (id, valorEntrada, qtdParcelas, idProduto) VALUES (?, ?, ?, ?)";
            $stmt = $this->conexao->prepare($sql);
            
            $stmt->execute([$id, $valorEntrada, $qtdParcelas, $idProduto]);
            
            return $id;
        } catch (Exception $e) {
            throw new Exception("Erro ao criar compra: " . $e->getMessage());
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM compras WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar compra: " . $e->getMessage());
        }
    }

    public function listarTodos()
    {
        try {
            $sql = "SELECT c.*, p.nome as nome_produto, p.valor as valor_produto 
                    FROM compras c 
                    INNER JOIN produtos p ON c.idProduto = p.id 
                    ORDER BY c.id";
            
            $stmt = $this->conexao->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao listar compras: " . $e->getMessage());
        }
    }

    public function atualizar($id, $valorEntrada, $qtdParcelas, $idProduto)
    {
        try {
            $sql = "UPDATE compras SET valorEntrada = ?, qtdParcelas = ?, idProduto = ? WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            
            $stmt->execute([$valorEntrada, $qtdParcelas, $idProduto, $id]);
            
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar compra: " . $e->getMessage());
        }
    }

    public function deletar($id)
    {
        try {
            $sql = "DELETE FROM compras WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            
            $stmt->execute([$id]);
            
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar compra: " . $e->getMessage());
        }
    }
}