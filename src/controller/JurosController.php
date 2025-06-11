<?php

namespace Src\Controller;

use PDO;
use Exception;
use Src\Config\Conexao;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class JurosController
{
    public function atualizarJuros(Request $request, Response $response, array $args): Response
    {
        $dados = $request->getParsedBody();
        $dataInicio = $dados['dataInicio'] ?? null;
        $dataFinal = $dados['dataFinal'] ?? null;

        if (
            !$dataInicio || !$dataFinal ||
            $dataInicio < '2010-01-01' || $dataFinal < $dataInicio || $dataFinal > date('Y-m-d')
        ) {
            return $response->withStatus(400);
        }

        $dataInicioBC = date("d/m/Y", strtotime($dataInicio));
        $dataFinalBC = date("d/m/Y", strtotime($dataFinal));
        $url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.4189/dados?formato=json&dataInicial=$dataInicioBC&dataFinal=$dataFinalBC";

        $apiResponse = file_get_contents($url);
        $selicData = json_decode($apiResponse, true);

        if (!$selicData) {
            $response->getBody()->write(json_encode(["erro" => "Erro na API do Banco Central"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $soma = 0;
        foreach ($selicData as $item) {
            $soma += (float) str_replace(',', '.', $item['valor']);
        }
        $media = $soma / count($selicData);

        try {
            $pdo = Conexao::getConnection();
            $stmt = $pdo->prepare("INSERT INTO taxaJuros (data_inicio, data_final, taxa) VALUES (:inicio, :final, :taxa)");
            $stmt->bindParam(':inicio', $dataInicio);
            $stmt->bindParam(':final', $dataFinal);
            $stmt->bindParam(':taxa', $media);
            $stmt->execute();

            $response->getBody()->write(json_encode(["novaTaxa" => round($media, 2)]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["erro" => "Erro ao salvar taxa: " . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}