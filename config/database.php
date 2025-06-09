<?php
function getConnection() {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "loja";

    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao conectar ao banco de dados."]);
        exit;
    }

    return $conn;
}
