<?php

namespace Src\Service;

use Src\DAO\ParcelaDAO;
use Src\Model\Parcela;
use Exception;
use DateTime;
use DateInterval;

class ParcelasService
{
    private $parcelaDAO;

    public function __construct()
    {
        $this->parcelaDAO = new ParcelaDAO();
    }

    public function processarCompra($idCompra, $valorEntrada, $qtdParcelas, $valorProduto)
    {
        try {
            // Validações básicas
            if ($valorProduto < 0) {
                return ['erro' => 'Valor do produto não pode ser negativo'];
            }

            if ($valorEntrada < 0) {
                return ['erro' => 'Valor de entrada não pode ser negativo'];
            }

            if ($qtdParcelas < 0) {
                return ['erro' => 'Quantidade de parcelas não pode ser negativa'];
            }

            // Calcular valor restante após entrada
            $valorRestante = $valorProduto - $valorEntrada;
            
            // Se não há valor restante, não há parcelas
            if ($valorRestante <= 0) {
                return [
                    'valorTotal' => $valorProduto,
                    'valorEntrada' => $valorEntrada,
                    'valorRestante' => 0,
                    'qtdParcelas' => 0,
                    'valorParcela' => 0,
                    'parcelas' => []
                ];
            }

            // Se há valor restante mas não há parcelas definidas, erro
            if ($qtdParcelas <= 0) {
                return [
                    'erro' => 'Para valor restante maior que zero, é necessário definir quantidade de parcelas maior que zero'
                ];
            }

            // Calcular valor de cada parcela
            $valorParcela = round($valorRestante / $qtdParcelas, 2);
            
            // Ajustar última parcela para diferenças de arredondamento
            $somaParcelasCalculadas = $valorParcela * ($qtdParcelas - 1);
            $ultimaParcela = round($valorRestante - $somaParcelasCalculadas, 2);

            // Validar se a última parcela não ficou negativa
            if ($ultimaParcela < 0) {
                return ['erro' => 'Erro no cálculo das parcelas'];
            }

            // Gerar e salvar parcelas no banco
            $parcelas = [];
            $dataVencimento = new DateTime();
            
            for ($i = 1; $i <= $qtdParcelas; $i++) {
                $dataVencimento->add(new DateInterval('P1M')); // Adiciona 1 mês
                
                $valorParcelaAtual = ($i === $qtdParcelas) ? $ultimaParcela : $valorParcela;
                
                // Criar objeto Parcela
                $parcela = new Parcela(
                    null, // ID será gerado pelo banco
                    $idCompra,
                    $i,
                    $valorParcelaAtual,
                    $dataVencimento->format('Y-m-d'),
                    'pendente'
                );
                
                // Inserir no banco
                $resultado = $this->parcelaDAO->inserir($parcela);
                
                if (!$resultado) {
                    return ['erro' => 'Erro ao inserir parcela ' . $i];
                }
                
                $parcelas[] = [
                    'numero' => $i,
                    'valor' => $valorParcelaAtual,
                    'dataVencimento' => $dataVencimento->format('Y-m-d'),
                    'status' => 'pendente'
                ];
            }

            return [
                'valorTotal' => $valorProduto,
                'valorEntrada' => $valorEntrada,
                'valorRestante' => $valorRestante,
                'qtdParcelas' => $qtdParcelas,
                'valorParcela' => $valorParcela,
                'parcelas' => $parcelas
            ];

        } catch (Exception $e) {
            return ['erro' => 'Erro ao processar parcelas: ' . $e->getMessage()];
        }
    }

    public function calcularJuros($valorParcela, $diasAtraso, $taxaJurosDiaria = 0.033)
    {
        try {
            if ($diasAtraso <= 0 || $valorParcela <= 0) {
                return 0;
            }

            if ($taxaJurosDiaria < 0) {
                return 0;
            }

            return round($valorParcela * ($taxaJurosDiaria / 100) * $diasAtraso, 2);
        } catch (Exception $e) {
            return 0;
        }
    }

    public function verificarVencimento($dataVencimento)
    {
        try {
            if (empty($dataVencimento)) {
                return ['erro' => 'Data de vencimento não informada'];
            }

            $hoje = new DateTime();
            $vencimento = new DateTime($dataVencimento);
            
            $diferenca = $hoje->diff($vencimento);
            
            if ($hoje > $vencimento) {
                return [
                    'vencida' => true,
                    'diasAtraso' => $diferenca->days
                ];
            }

            return [
                'vencida' => false,
                'diasParaVencimento' => $diferenca->days
            ];
        } catch (Exception $e) {
            return ['erro' => 'Erro ao verificar vencimento: ' . $e->getMessage()];
        }
    }
}