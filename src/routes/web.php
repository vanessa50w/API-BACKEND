<?php


use Slim\Routing\RouteCollectorProxy;
use Src\controller\ProdutoController;

// Define o grupo de rotas relacionadas a /produtos
$app->group('/produtos', function (RouteCollectorProxy $group) {
    $group->post('', [ProdutoController::class, 'store']);
});
