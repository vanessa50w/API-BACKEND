<?php

namespace Src\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Src\DAO\ParcelaDAO;
use Exception;

class ParcelaController 
{
    private $parcelaDAO;

    public function __construct() 
    {
        $this->parcelaDAO = new ParcelaDAO();
    }

    public function consultarParcelas(Request $request, Response $response, array $args): Response 
    {
        try {
            // Obter parâmetros da query string
            $queryParams = $request->getQueryParams();
            $idCompra = $queryParams['idCompra'] ?? null;

            // Validar parâmetro obrigatório
            if (!$idCompra) {
                $response->getBody()->write(json_encode([
                    'erro' => 'Parâmetro idCompra é obrigatório'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }

            // Validar se é um número válido
            if (!is_numeric($idCompra) || $idCompra <= 0) {
                $response->getBody()->write(json_encode([
                    'erro' => 'Parâmetro idCompra deve ser um número válido'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }

            // Buscar parcelas
            $parcelas = $this->parcelaDAO->buscarPorCompra($idCompra);

            // Verificar se encontrou parcelas
            if (empty($parcelas)) {
                $response->getBody()->write(json_encode([
                    'erro' => 'Nenhuma parcela encontrada para esta compra'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }

            // Converter parcelas para array
            $parcelasArray = array_map(function($parcela) {
                return $parcela->toArray();
            }, $parcelas);

            // Resposta de sucesso
            $responseData = [
                'sucesso' => true,
                'idCompra' => (int)$idCompra,
                'totalParcelas' => count($parcelas),
                'parcelas' => $parcelasArray
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (Exception $e) {
            // Log do erro (você pode implementar um sistema de log aqui)
            error_log("Erro ao consultar parcelas: " . $e->getMessage());

            $response->getBody()->write(json_encode([
                'erro' => 'Erro interno do servidor'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    public function consultarParcela(Request $request, Response $response, array $args): Response 
    {
        try {
            $id = $args['id'] ?? null;

            if (!$id || !is_numeric($id)) {
                $response->getBody()->write(json_encode([
                    'erro' => 'ID da parcela inválido'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }

            $parcela = $this->parcelaDAO->buscarPorId($id);

            if (!$parcela) {
                $response->getBody()->write(json_encode([
                    'erro' => 'Parcela não encontrada'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'sucesso' => true,
                'parcela' => $parcela->toArray()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (Exception $e) {
            error_log("Erro ao consultar parcela: " . $e->getMessage());

            $response->getBody()->write(json_encode([
                'erro' => 'Erro interno do servidor'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    public function atualizarParcela(Request $request, Response $response, array $args): Response 
    {
        try {
            $id = $args['id'] ?? null;
            $data = $request->getParsedBody();

            if (!$id || !is_numeric($id)) {
                $response->getBody()->write(json_encode([
                    'erro' => 'ID da parcela inválido'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }

            // Buscar parcela existente
            $parcela = $this->parcelaDAO->buscarPorId($id);
            if (!$parcela) {
                $response->getBody()->write(json_encode([
                    'erro' => 'Parcela não encontrada'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }

            // Atualizar campos permitidos
            if (isset($data['situacao'])) {
                $parcela->setSituacao($data['situacao']);
            }
            if (isset($data['dataPagamento'])) {
                $parcela->setDataPagamento($data['dataPagamento']);
            }

            // Salvar alterações
            $sucesso = $this->parcelaDAO->atualizar($parcela);

            if ($sucesso) {
                $response->getBody()->write(json_encode([
                    'sucesso' => true,
                    'mensagem' => 'Parcela atualizada com sucesso',
                    'parcela' => $parcela->toArray()
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                $response->getBody()->write(json_encode([
                    'erro' => 'Erro ao atualizar parcela'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
            }

        } catch (Exception $e) {
            error_log("Erro ao atualizar parcela: " . $e->getMessage());

            $response->getBody()->write(json_encode([
                'erro' => 'Erro interno do servidor'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}