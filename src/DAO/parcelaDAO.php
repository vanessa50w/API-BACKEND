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
        $this->conexao = Conexao::getConnection();
    }

    public function buscarPorCompra($idCompra) 
    {
        try {
            $query = "SELECT * FROM parcelas WHERE idCompra = :idCompra ORDER BY numeroParcela";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindParam(':idCompra', $idCompra, PDO::PARAM_STR);
            $stmt->execute();
            
            $parcelas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $parcela = new Parcela(
                    $row['id'],
                    $row['idCompra'],
                    $row['numeroParcela'],
                    $row['valorParcela'],
                    $row['dataVencimento'],
                    $row['taxaJuros'] ?? null,
                    $row['created_at'] ?? null,
                    $row['updated_at'] ?? null
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
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                return new Parcela(
                    $row['id'],
                    $row['idCompra'],
                    $row['numeroParcela'],
                    $row['valorParcela'],
                    $row['dataVencimento'],
                    $row['taxaJuros'] ?? null,
                    $row['created_at'] ?? null,
                    $row['updated_at'] ?? null
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
            $query = "INSERT INTO parcelas 
                (id, idCompra, numeroParcela, valorParcela, dataVencimento, taxaJuros, created_at, updated_at) 
                VALUES 
                (:id, :idCompra, :numeroParcela, :valorParcela, :dataVencimento, :taxaJuros, :createdAt, :updatedAt)";
            
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', $parcela->getId());
            $stmt->bindValue(':idCompra', $parcela->getIdCompra());
            $stmt->bindValue(':numeroParcela', $parcela->getNumeroParcela());
            $stmt->bindValue(':valorParcela', $parcela->getValorParcela());
            $stmt->bindValue(':dataVencimento', $parcela->getDataVencimento());
            $stmt->bindValue(':taxaJuros', $parcela->getTaxaJuros());
            $stmt->bindValue(':createdAt', $parcela->getCreatedAt());
            $stmt->bindValue(':updatedAt', $parcela->getUpdatedAt());
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            throw new Exception("Erro ao inserir parcela: " . $e->getMessage());
        }
    }

    public function atualizar(Parcela $parcela) 
    {
        try {
            $query = "UPDATE parcelas SET 
                      dataVencimento = :dataVencimento,
                      taxaJuros = :taxaJuros,
                      updated_at = :updatedAt
                      WHERE id = :id";
            
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':dataVencimento', $parcela->getDataVencimento());
            $stmt->bindValue(':taxaJuros', $parcela->getTaxaJuros());
            $stmt->bindValue(':updatedAt', $parcela->getUpdatedAt());
            $stmt->bindValue(':id', $parcela->getId());
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar parcela: " . $e->getMessage());
        }
    }

    public function deletarPorCompra($idCompra)
    {
        try {
            $query = "DELETE FROM parcelas WHERE idCompra = :idCompra";
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
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar parcela: " . $e->getMessage());
        }
    }
}