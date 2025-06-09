<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/SelicService.php';

class JurosController {
    public static function atualizar() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $dataInicio = $input['dataInicio'] ?? null;
        $dataFinal = $input['dataFinal'] ?? null;

        if (!$dataInicio || !$dataFinal) {
            http_response_code(400);
            echo json_encode(['erro' => 'Campos dataInicio e dataFinal são obrigatórios.']);
            return;
        }

        if ($dataInicio < '2010-01-01' || $dataFinal < $dataInicio || $dataFinal > date('Y-m-d')) {
            http_response_code(400);
            echo json_encode(['erro' => 'Datas inválidas.']);
            return;
        }

        $media = SelicService::buscarTaxa($dataInicio, $dataFinal);
        if ($media === null) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao consultar a taxa SELIC.']);
            return;
        }

        $conn = getConnection();
        $id = uniqid();

        $stmt = $conn->prepare("INSERT INTO taxas_juros (id, data_inicio, data_final, taxa) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $id, $dataInicio, $dataFinal, $media);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        echo json_encode([
            'mensagem' => 'Taxa registrada com sucesso',
            'taxa' => round($media, 2),
            'dataInicio' => $dataInicio,
            'dataFinal' => $dataFinal
        ]);
    }
}
