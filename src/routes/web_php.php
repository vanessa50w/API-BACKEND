<?php

use Src\Controller\ProdutoController;
use Src\Controller\ParcelaController;
use Src\Controller\CompraController;
use Src\Controller\JurosController;

$produtoController = new ProdutoController();
$parcelaController = new ParcelaController();
$compraController = new CompraController();


// Rotas para produtos
$app->get('/produtos', [$produtoController, 'listar']);
$app->get('/produtos/{id}', [$produtoController, 'buscarPorId']);
$app->post('/produtos', [$produtoController, 'criar']);
$app->put('/produtos/{id}', [$produtoController, 'atualizar']);
$app->delete('/produtos/{id}', [$produtoController, 'deletar']);

// Listar parcelas de uma compra (por query string: /parcelas?idCompra=...)
$app->get('/parcelas', [$parcelaController, 'consultarParcelas']);

// Consultar uma parcela específica
$app->get('/parcelas/{id}', [$parcelaController, 'consultarParcela']);

// Atualizar uma parcela específica
$app->put('/parcelas/{id}', [$parcelaController, 'atualizarParcela']);

// Rotas para compras
$app->post('/compras', [$compraController, 'criarCompra']);
$app->get('/compras', [$compraController, 'listarCompras']);
$app->get('/compras/{id}', [$compraController, 'buscarCompra']);
$app->put('/compras/{id}', [$compraController, 'atualizarCompra']);
$app->delete('/compras/{id}', [$compraController, 'deletarCompra']);

$app->post('/juros/atualizar', [JurosController::class, 'atualizarJuros']);

// Rota de teste
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode(['mensagem' => 'API funcionando!']));
    return $response->withHeader('Content-Type', 'application/json');
});