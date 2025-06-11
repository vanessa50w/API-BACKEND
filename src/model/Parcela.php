<?php

namespace Src\Model;

class Parcela 
{
    private $id;
    private $idCompra;
    private $numeroParcela;
    private $valorParcela;
    private $dataVencimento;
    private $situacao;
    private $dataPagamento;
    private $taxaJuros;
    private $createdAt;
    private $updatedAt;

    public function __construct($id = null, $idCompra = null, $numeroParcela = null, 
                              $valorParcela = null, $dataVencimento = null, 
                              $situacao = null, $dataPagamento = null) 
    {
        $this->id = $id;
        $this->idCompra = $idCompra;
        $this->numeroParcela = $numeroParcela;
        $this->valorParcela = $valorParcela;
        $this->dataVencimento = $dataVencimento;
        $this->situacao = $situacao;
        $this->dataPagamento = $dataPagamento;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getIdCompra() { return $this->idCompra; }
    public function getNumeroParcela() { return $this->numeroParcela; }
    public function getValorParcela() { return $this->valorParcela; }
    public function getDataVencimento() { return $this->dataVencimento; }
    public function getSituacao() { return $this->situacao; }
    public function getDataPagamento() { return $this->dataPagamento; }
    public function getTaxaJuros() {
        return $this->taxaJuros;
    }
    public function getCreatedAt() {
        return $this->createdAt;
    }
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setIdCompra($idCompra) { $this->idCompra = $idCompra; }
    public function setNumeroParcela($numeroParcela) { $this->numeroParcela = $numeroParcela; }
    public function setValorParcela($valorParcela) { $this->valorParcela = $valorParcela; }
    public function setDataVencimento($dataVencimento) { $this->dataVencimento = $dataVencimento; }
    public function setSituacao($situacao) { $this->situacao = $situacao; }
    public function setDataPagamento($dataPagamento) { $this->dataPagamento = $dataPagamento; }
    public function setTaxaJuros($taxaJuros) {
        $this->taxaJuros = $taxaJuros;
    }
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    public function toArray() 
    {
        return [
            'id' => $this->id,
            'idCompra' => $this->idCompra,
            'numeroParcela' => $this->numeroParcela,
            'valorParcela' => $this->valorParcela,
            'dataVencimento' => $this->dataVencimento,
            'situacao' => $this->situacao,
            'dataPagamento' => $this->dataPagamento
        ];
    }
}