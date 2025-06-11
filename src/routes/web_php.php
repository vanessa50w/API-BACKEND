<?php

use Src\Controller\ProdutoController;

require_once __DIR__ . '/../config/conexao.php';

$produtoController = new ProdutoController();

// Rotas para produtos
$app->get('/produtos', [$produtoController, 'listar']);
$app->get('/produtos/{id}', [$produtoController, 'buscarPorId']);
$app->post('/produtos', [$produtoController, 'criar']);
$app->put('/produtos/{id}', [$produtoController, 'atualizar']);
$app->delete('/produtos/{id}', [$produtoController, 'deletar']);

// Rota de teste
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode(['mensagem' => 'API funcionando!']));
    return $response->withHeader('Content-Type', 'application/json');
});