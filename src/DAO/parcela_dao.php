<?php

namespace Src\DAO;

use Src\Config\Conexao;
use Src\Model\Parcela;
use PDO;
use Exception;

class ParcelaDAO 
{
    private $conexao;

    public function __construct() 
    {
        $this->conexao = Conexao::conectar();
    }

    public function buscarPorCompra($idCompra) 
    {
        try {
            $query = "SELECT * FROM parcelas WHERE id_compra = :idCompra ORDER BY numero_parcela";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindParam(':idCompra', $idCompra, PDO::PARAM_STR);
            $stmt->execute();
            
            $parcelas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $parcela = new Parcela(
                    $row['id'],
                    $row['id_compra'],
                    $row['numero_parcela'],
                    $row['valor_parcela'],
                    $row['data_vencimento'],
                    $row['situacao'],
                    $row['data_pagamento']
                );
                $parcelas[] = $parcela;
            }
            
            return $parcelas;
            
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar parcelas: " . $e->getMessage());
        }
    }

    public function buscarPorId($id) 
    {
        try {
            $query = "SELECT * FROM parcelas WHERE id = :id";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                return new Parcela(
                    $row['id'],
                    $row['id_compra'],
                    $row['numero_parcela'],
                    $row['valor_parcela'],
                    $row['data_vencimento'],
                    $row['situacao'],
                    $row['data_pagamento']
                );
            }
            
            return null;
            
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar parcela: " . $e->getMessage());
        }
    }

    public function inserir(Parcela $parcela) 
    {
        try {
            $query = "INSERT INTO parcelas (id_compra, numero_parcela, valor_parcela, data_vencimento, situacao) 
                      VALUES (:idCompra, :numeroParcela, :valorParcela, :dataVencimento, :situacao)";
            
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':idCompra', $parcela->getIdCompra());
            $stmt->bindValue(':numeroParcela', $parcela->getNumeroParcela());
            $stmt->bindValue(':valorParcela', $parcela->getValorParcela());
            $stmt->bindValue(':dataVencimento', $parcela->getDataVencimento());
            $stmt->bindValue(':situacao', $parcela->getSituacao());
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            throw new Exception("Erro ao inserir parcela: " . $e->getMessage());
        }
    }

    public function atualizar(Parcela $parcela) 
    {
        try {
            $query = "UPDATE parcelas SET 
                      situacao = :situacao, 
                      data_pagamento = :dataPagamento 
                      WHERE id = :id";
            
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':situacao', $parcela->getSituacao());
            $stmt->bindValue(':dataPagamento', $parcela->getDataPagamento());
            $stmt->bindValue(':id', $parcela->getId());
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar parcela: " . $e->getMessage());
        }
    }

    public function deletarPorCompra($idCompra)
    {
        try {
            $query = "DELETE FROM parcelas WHERE id_compra = :idCompra";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindParam(':idCompra', $idCompra, PDO::PARAM_STR);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar parcelas: " . $e->getMessage());
        }
    }

    public function deletar($id)
    {
        try {
            $query = "DELETE FROM parcelas WHERE id = :id";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar parcela: " . $e->getMessage());
        }
    }
}