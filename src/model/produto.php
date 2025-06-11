<?php

namespace Src\Model;

class Produto {
    private $id;
    private $nome;
    private $valor;
    private $tipo;

    public function __construct($id, $nome, $valor, $tipo = null) {
        if (empty($id) || empty($nome) || empty($valor)) {
            throw new \InvalidArgumentException('id, nome e valor são obrigatórios');
        }
        $this->id = $id;
        $this->nome = $nome;
        $this->valor = $valor;
        $this->tipo = $tipo;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getValor() {
        return $this->valor;
    }

    public function setValor($valor) {
        $this->valor = $valor;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'valor' => $this->valor,
            'tipo' => $this->tipo
        ];
    }
}