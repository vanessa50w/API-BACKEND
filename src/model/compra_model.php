<?php

namespace Src\Model;

class Compra
{
    private $id;
    private $valorEntrada;
    private $qtdParcelas;
    private $idProduto;
    private $dataCompra;

    public function __construct($id = null, $valorEntrada = null, $qtdParcelas = null, $idProduto = null)
    {
        $this->id = $id;
        $this->valorEntrada = $valorEntrada;
        $this->qtdParcelas = $qtdParcelas;
        $this->idProduto = $idProduto;
        $this->dataCompra = date('Y-m-d H:i:s');
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getValorEntrada()
    {
        return $this->valorEntrada;
    }

    public function getQtdParcelas()
    {
        return $this->qtdParcelas;
    }

    public function getIdProduto()
    {
        return $this->idProduto;
    }

    public function getDataCompra()
    {
        return $this->dataCompra;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setValorEntrada($valorEntrada)
    {
        $this->valorEntrada = $valorEntrada;
    }

    public function setQtdParcelas($qtdParcelas)
    {
        $this->qtdParcelas = $qtdParcelas;
    }

    public function setIdProduto($idProduto)
    {
        $this->idProduto = $idProduto;
    }

    public function setDataCompra($dataCompra)
    {
        $this->dataCompra = $dataCompra;
    }

    // Validações
    public function validar()
    {
        $erros = [];

        if (empty($this->id)) {
            $erros[] = 'ID é obrigatório';
        }

        if ($this->valorEntrada < 0) {
            $erros[] = 'Valor de entrada não pode ser negativo';
        }

        if ($this->qtdParcelas < 0) {
            $erros[] = 'Quantidade de parcelas não pode ser negativa';
        }

        if (empty($this->idProduto)) {
            $erros[] = 'ID do produto é obrigatório';
        }

        return $erros;
    }

    // Converter para array
    public function toArray()
    {
        return [
            'id' => $this->id,
            'valorEntrada' => $this->valorEntrada,
            'qtdParcelas' => $this->qtdParcelas,
            'idProduto' => $this->idProduto,
            'dataCompra' => $this->dataCompra
        ];
    }
}