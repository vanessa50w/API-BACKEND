<?php

namespace Src\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Src\DAO\ProdutoDAO;

class ProdutoController
{
    public static function store(Request $request, Response $response)
    {
        $dados = $request->getParsedBody();

        // Validação básica
        if (!isset($dados['id'], $dados['nome'], $dados['valor'])) {
            $response->getBody()->write(json_encode(['erro' => 'Campos obrigatórios: id, nome e valor']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        if (!self::validarUUID($dados['id'])) {
            $response->getBody()->write(json_encode(['erro' => 'ID deve estar no formato UUID']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        if (!is_numeric($dados['valor']) || $dados['valor'] < 0) {
            $response->getBody()->write(json_encode(['erro' => 'O valor deve ser um número maior ou igual a 0']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        // Tenta salvar no banco
        $salvo = ProdutoDAO::salvar(
            $dados['id'],
            $dados['nome'],
            $dados['tipo'] ?? null,
            $dados['valor']
        );

        if (!$salvo) {
            $response->getBody()->write(json_encode(['erro' => 'Produto já existe com esse ID']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        // Sucesso
        $response->getBody()->write(json_encode(['mensagem' => 'Produto cadastrado com sucesso']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    // Função para validar UUID simples
    private static function validarUUID($uuid)
    {
        return preg_match('/^[a-f0-9\-]{36}$/i', $uuid);
    }
}
