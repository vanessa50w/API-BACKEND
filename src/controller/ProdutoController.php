<?php

namespace Src\Controller;

use Src\DAO\ProdutoDAO;
use Src\Model\Produto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

function gerarUuidV4() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

class ProdutoController {
    private $produtoDAO;

    public function __construct() {
        $this->produtoDAO = new ProdutoDAO();
    }

    public function listar(Request $request, Response $response) {
        try {
            $produtos = $this->produtoDAO->buscarTodos();
            $produtosArray = array_map(function($produto) {
                return $produto->toArray();
            }, $produtos);
            
            $response->getBody()->write(json_encode($produtosArray));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['erro' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function buscarPorId(Request $request, Response $response, array $args) {
        try {
            $id = $args['id'];
            $produto = $this->produtoDAO->buscarPorId($id);
            
            if ($produto) {
                $response->getBody()->write(json_encode($produto->toArray()));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['erro' => 'Produto não encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['erro' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function criar(Request $request, Response $response) {
        try {
            $dados = $request->getParsedBody();

            if (empty($dados['id'])) {
            $dados['id'] = gerarUuidV4(); // ou use ramsey/uuid
        }

            if (!isset($dados['id']) || !self::validarUUID($dados['id'])) {
            $response->getBody()->write(json_encode(['erro' => 'ID deve estar no formato UUID']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }
            
            if (!isset($dados['nome']) || !isset($dados['valor'])) {
                $response->getBody()->write(json_encode(['erro' => 'Nome e valor são obrigatórios']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            
            $produto = new Produto(
                $dados['id'],
                $dados['nome'],
                $dados['valor'],
                $dados['tipo'] ?? null
            );
            
            $produtoCriado = $this->produtoDAO->criar($produto);
            
            $response->getBody()->write(json_encode($produtoCriado->toArray()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['erro' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function atualizar(Request $request, Response $response, array $args) {
        try {
            $id = $args['id'];
            $dados = $request->getParsedBody();
            
            $produto = $this->produtoDAO->buscarPorId($id);
            if (!$produto) {
                $response->getBody()->write(json_encode(['erro' => 'Produto não encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
            $produto->setNome($dados['nome'] ?? $produto->getNome());
            $produto->setValor($dados['valor'] ?? $produto->getValor());
            $produto->setTipo($dados['tipo'] ?? $produto->getTipo());
            
            $this->produtoDAO->atualizar($produto);
            
            $response->getBody()->write(json_encode($produto->toArray()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['erro' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deletar(Request $request, Response $response, array $args) {
        try {
            $id = $args['id'];
            
            $produto = $this->produtoDAO->buscarPorId($id);
            if (!$produto) {
                $response->getBody()->write(json_encode(['erro' => 'Produto não encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
            $this->produtoDAO->deletar($id);
            
            $response->getBody()->write(json_encode(['mensagem' => 'Produto deletado com sucesso']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['erro' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // Função para validar UUID simples
private static function validarUUID($uuid)
{
    return preg_match('/^[a-f0-9\-]{36}$/i', $uuid);
}
}