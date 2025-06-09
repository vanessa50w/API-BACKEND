<?php
require_once __DIR__ . '/../src/JurosController.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');


if ($method === 'PUT' && $uri === '/juros') {
    JurosController::atualizar();
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Endpoint não encontrado.']);
}
