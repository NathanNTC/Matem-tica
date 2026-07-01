<?php

namespace App\Services;

class BissecaoService
{
    public function calcular(
        string $funcao,
        float $a,
        float $b,
        float $tolerancia,
        FuncaoService $funcaoService
    ): array {

        $iteracoes = [];

        $erro = abs($b - $a);
        $contador = 0;

        while ($erro > $tolerancia) {

            $xm = ($a + $b) / 2;

            $fa = $funcaoService->avaliar($funcao, $a);
            $fb = $funcaoService->avaliar($funcao, $b);
            $fxm = $funcaoService->avaliar($funcao, $xm);

            $iteracoes[] = [
                'iteracao' => $contador,
                'a' => $a,
                'b' => $b,
                'xm' => $xm,
                'fa' => $fa,
                'fb' => $fb,
                'fxm' => $fxm,
                'erro' => $erro,
            ];

            if ($fa * $fxm < 0) {
                $b = $xm;
            } else {
                $a = $xm;
            }

            $erro = abs($b - $a);

            $contador++;

            if ($contador > 1000) {
                break;
            }
        }

        return [
            'raiz' => ($a + $b) / 2,
            'iteracoes' => $iteracoes,
            'total_iteracoes' => count($iteracoes),
        ];
    }
}