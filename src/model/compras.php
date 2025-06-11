<?php

$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'loja';

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    http_response_code(500);
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

$entradaJson = file_get_contents('php://input');
$dados = json_decode($entradaJson, true);

if (!$dados || !is_array($dados)) {
    http_response_code(400);
    exit();
}

$camposObrigatorios = ['id', 'valorEntrada', 'qtdParcelas', 'idProduto'];
foreach ($camposObrigatorios as $campo) {
    if (!isset($dados[$campo])) {
        http_response_code(400);
        exit();
    }
}

$id = $conexao->real_escape_string($dados['id']);
$valorEntrada = floatval($dados['valorEntrada']);
$qtdParcelas = intval($dados['qtdParcelas']);
$idProduto = $conexao->real_escape_string($dados['idProduto']);

if ($qtdParcelas < 0) {
    http_response_code(422);
    exit();
}

$verificaProduto = "SELECT valor FROM produtos WHERE id = '$idProduto'";
$resultado = $conexao->query($verificaProduto);

if ($resultado->num_rows === 0) {
    http_response_code(422);
    exit();
}

$produto = $resultado->fetch_assoc();
$valorProduto = floatval($produto['valor']);

if ($valorEntrada > $valorProduto) {
    http_response_code(422);
    exit();
}

$verificaId = "SELECT id FROM compras WHERE id = '$id'";
$resultadoId = $conexao->query($verificaId);

if ($resultadoId->num_rows > 0) {
    http_response_code(422);
    exit();
}

$sql = "INSERT INTO compras (id, valorEntrada, qtdParcelas, idProduto)
        VALUES ('$id', $valorEntrada, $qtdParcelas, '$idProduto')";

if ($conexao->query($sql)) {
    http_response_code(201);
} else {
    http_response_code(500);
}
