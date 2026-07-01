<?php

namespace App\Services;

class NewtonRaphsonService
{
    public function calcular(
        string $funcao,
        float $x0,
        float $tolerancia,
        FuncaoService $funcaoService
    ): array {

        $iteracoes = [];

        $h = 0.000001;
        $erro = PHP_FLOAT_MAX;
        $contador = 0;

        while ($erro > $tolerancia) {

            $fx = $funcaoService->avaliar($funcao, $x0);

            $derivada =
                (
                    $funcaoService->avaliar($funcao, $x0 + $h)
                    -
                    $funcaoService->avaliar($funcao, $x0 - $h)
                ) / (2 * $h);

            if (abs($derivada) < 0.00000001) {
                break;
            }

            $x1 = $x0 - ($fx / $derivada);

            $erro = abs($x1 - $x0);

            $iteracoes[] = [
                'iteracao' => $contador,
                'xn' => $x0,
                'fx' => $fx,
                'derivada' => $derivada,
                'xn1' => $x1,
                'erro' => $erro,
            ];

            $x0 = $x1;

            $contador++;

            if ($contador > 1000) {
                break;
            }
        }

        return [
            'raiz' => $x0,
            'iteracoes' => $iteracoes,
            'total_iteracoes' => count($iteracoes),
        ];
    }
}