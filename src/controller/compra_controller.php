<?php

namespace Src\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Src\DAO\CompraDAO;
use Src\DAO\ProdutoDAO;
use Src\DAO\ParcelaDAO;
use Src\Service\ParcelasService;
use Exception;

class CompraController
{
    private $compraDAO;
    private $produtoDAO;
    private $parcelaDAO;
    private $parcelasService;

    public function __construct()
    {
        $this->compraDAO = new CompraDAO();
        $this->produtoDAO = new ProdutoDAO();
        $this->parcelaDAO = new ParcelaDAO();
        $this->parcelasService = new ParcelasService();
    }

    public function criarCompra(Request $request, Response $response): Response
    {
        try {
            $dados = $request->getParsedBody();

            // Validar JSON
            if (!$dados || !is_array($dados)) {
                $response->getBody()->write(json_encode(['erro' => 'JSON inválido']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Validar campos obrigatórios
            $camposObrigatorios = ['id', 'valorEntrada', 'qtdParcelas', 'idProduto'];
            foreach ($camposObrigatorios as $campo) {
                if (!isset($dados[$campo])) {
                    $response->getBody()->write(json_encode(['erro' => "Campo '$campo' é obrigatório"]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            $id = $dados['id'];
            $valorEntrada = floatval($dados['valorEntrada']);
            $qtdParcelas = intval($dados['qtdParcelas']);
            $idProduto = $dados['idProduto'];

            // Validações de negócio
            if ($qtdParcelas < 0) {
                $response->getBody()->write(json_encode(['erro' => 'Quantidade de parcelas não pode ser negativa']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            if ($valorEntrada < 0) {
                $response->getBody()->write(json_encode(['erro' => 'Valor de entrada não pode ser negativo']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Verificar se produto existe
            $produto = $this->produtoDAO->buscarPorId($idProduto);
            if (!$produto) {
                $response->getBody()->write(json_encode(['erro' => 'Produto não encontrado']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            $valorProduto = floatval($produto['valor']);

            // Validar valor de entrada
            if ($valorEntrada > $valorProduto) {
                $response->getBody()->write(json_encode(['erro' => 'Valor de entrada não pode ser maior que o valor do produto']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Verificar se ID da compra já existe
            if ($this->compraDAO->buscarPorId($id)) {
                $response->getBody()->write(json_encode(['erro' => 'ID da compra já existe']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Validação adicional: se há valor restante, deve ter parcelas
            $valorRestante = $valorProduto - $valorEntrada;
            if ($valorRestante > 0 && $qtdParcelas <= 0) {
                $response->getBody()->write(json_encode(['erro' => 'Para valor restante maior que zero, é necessário definir quantidade de parcelas']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Criar compra
            $compraId = $this->compraDAO->criar($id, $valorEntrada, $qtdParcelas, $idProduto);
            
            // Processar parcelas
            $resultadoParcelas = $this->parcelasService->processarCompra($id, $valorEntrada, $qtdParcelas, $valorProduto);

            if (isset($resultadoParcelas['erro'])) {
                // Se houve erro, deletar a compra criada
                $this->compraDAO->deletar($id);
                $response->getBody()->write(json_encode($resultadoParcelas));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Resposta de sucesso
            $resposta = [
                'mensagem' => 'Compra registrada com sucesso',
                'id' => $id,
                'detalhes' => $resultadoParcelas
            ];

            $response->getBody()->write(json_encode($resposta));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            // Log do erro
            error_log("Erro ao criar compra: " . $e->getMessage());
            
            $response->getBody()->write(json_encode(['erro' => 'Erro interno do servidor']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function listarCompras(Request $request, Response $response): Response
    {
        try {
            $compras = $this->compraDAO->listarTodos();
            
            $response->getBody()->write(json_encode([
                'sucesso' => true,
                'compras' => $compras
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (Exception $e) {
            error_log("Erro ao listar compras: " . $e->getMessage());
            
            $response->getBody()->write(json_encode(['erro' => 'Erro interno do servidor']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function buscarCompra(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'] ?? null;

            if (!$id) {
                $response->getBody()->write(json_encode(['erro' => 'ID é obrigatório']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $compra = $this->compraDAO->buscarPorId($id);

            if (!$compra) {
                $response->getBody()->write(json_encode(['erro' => 'Compra não encontrada']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'sucesso' => true,
                'compra' => $compra
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (Exception $e) {
            error_log("Erro ao buscar compra: " . $e->getMessage());
            
            $response->getBody()->write(json_encode(['erro' => 'Erro interno do servidor']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function atualizarCompra(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'] ?? null;
            $dados = $request->getParsedBody();

            if (!$id) {
                $response->getBody()->write(json_encode(['erro' => 'ID é obrigatório']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Verificar se compra existe
            $compraExistente = $this->compraDAO->buscarPorId($id);
            if (!$compraExistente) {
                $response->getBody()->write(json_encode(['erro' => 'Compra não encontrada']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $valorEntrada = isset($dados['valorEntrada']) ? floatval($dados['valorEntrada']) : floatval($compraExistente['valorEntrada']);
            $qtdParcelas = isset($dados['qtdParcelas']) ? intval($dados['qtdParcelas']) : intval($compraExistente['qtdParcelas']);
            $idProduto = $dados['idProduto'] ?? $compraExistente['idProduto'];

            // Validações
            if ($qtdParcelas < 0) {
                $response->getBody()->write(json_encode(['erro' => 'Quantidade de parcelas não pode ser negativa']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            if ($valorEntrada < 0) {
                $response->getBody()->write(json_encode(['erro' => 'Valor de entrada não pode ser negativo']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Verificar se produto existe (se foi alterado)
            $produto = $this->produtoDAO->buscarPorId($idProduto);
            if (!$produto) {
                $response->getBody()->write(json_encode(['erro' => 'Produto não encontrado']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }
            
            $valorProduto = floatval($produto['valor']);
            if ($valorEntrada > $valorProduto) {
                $response->getBody()->write(json_encode(['erro' => 'Valor de entrada não pode ser maior que o valor do produto']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Validação adicional: se há valor restante, deve ter parcelas
            $valorRestante = $valorProduto - $valorEntrada;
            if ($valorRestante > 0 && $qtdParcelas <= 0) {
                $response->getBody()->write(json_encode(['erro' => 'Para valor restante maior que zero, é necessário definir quantidade de parcelas']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Antes de atualizar, deletar parcelas antigas se houver mudança significativa
            if ($valorEntrada != floatval($compraExistente['valorEntrada']) || 
                $qtdParcelas != intval($compraExistente['qtdParcelas']) || 
                $idProduto != $compraExistente['idProduto']) {
                
                // Deletar parcelas antigas
                $this->parcelaDAO->deletarPorCompra($id);
                
                // Recriar parcelas com novos valores
                $resultadoParcelas = $this->parcelasService->processarCompra($id, $valorEntrada, $qtdParcelas, $valorProduto);
                
                if (isset($resultadoParcelas['erro'])) {
                    $response->getBody()->write(json_encode($resultadoParcelas));
                    return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
                }
            }

            $atualizado = $this->compraDAO->atualizar($id, $valorEntrada, $qtdParcelas, $idProduto);

            if ($atualizado) {
                $response->getBody()->write(json_encode(['mensagem' => 'Compra atualizada com sucesso']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['erro' => 'Erro ao atualizar compra']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

        } catch (Exception $e) {
            error_log("Erro ao atualizar compra: " . $e->getMessage());
            
            $response->getBody()->write(json_encode(['erro' => 'Erro interno do servidor']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deletarCompra(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'] ?? null;

            if (!$id) {
                $response->getBody()->write(json_encode(['erro' => 'ID é obrigatório']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Deletar parcelas primeiro (integridade referencial)
            $this->parcelaDAO->deletarPorCompra($id);
            
            // Deletar compra
            $deletado = $this->compraDAO->deletar($id);

            if ($deletado) {
                $response->getBody()->write(json_encode(['mensagem' => 'Compra deletada com sucesso']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['erro' => 'Compra não encontrada']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

        } catch (Exception $e) {
            error_log("Erro ao deletar compra: " . $e->getMessage());
            
            $response->getBody()->write(json_encode(['erro' => 'Erro interno do servidor']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}