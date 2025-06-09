<?php
class SelicService {
    public static function buscarTaxa($dataInicio, $dataFinal) {
        $url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.4189/dados?formato=json&dataInicial=$dataInicio&dataFinal=$dataFinal";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $dados = json_decode($response, true);
        if (!$dados || count($dados) === 0) {
            return null;
        }

        $soma = 0;
        foreach ($dados as $registro) {
            $valor = str_replace(',', '.', $registro['valor']);
            $soma += (float) $valor;
        }

        return $soma / count($dados);
    }
}
