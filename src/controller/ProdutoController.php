<?php

namespace Src\Controller;

use Src\DAO\ProdutoDAO;
use Src\Model\Produto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;
use function Src\Utils\gerarUuidV4;


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

            if ($dados === null) { // JSON inválido ou não enviado
            return $response->withStatus(400);
        }

            if (empty($dados['id'])) {
            $dados['id'] = gerarUuidV4(); // ou use ramsey/uuid
        }

            if (isset($dados['valor']) && $dados['valor'] < 0) {
            return $response->withStatus(422);
        }

            if (!isset($dados['id']) || !self::validarUUID($dados['id'])) {
            return $response->withStatus(422);
        }
            
            if (!isset($dados['nome']) || !isset($dados['valor'])) {
                return $response->withStatus(422);
            }

            if ($dados === null) { // JSON inválido ou não enviado
            return $response->withStatus(400);
        }
            
            $produto = new Produto(
                $dados['id'],
                $dados['nome'],
                $dados['valor'],
                $dados['tipo'] ?? null
            );
            
            $this->produtoDAO->criar($produto);
            
            return $response->withStatus(201);
        } catch (Exception $e) {
            return $response->withStatus(400);
        }
    }

    public function atualizar(Request $request, Response $response, array $args) {
        try {
            $id = $args['id'];
            $dados = $request->getParsedBody();

            if (isset($dados['valor']) && $dados['valor'] < 0) {
            return $response->withStatus(422);
        }
            
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