<?php

namespace App\Services;

class BuscaRaizesService
{
    public function buscarIntervalos(
        string $funcao,
        float $min,
        float $max,
        float $passo,
        FuncaoService $funcaoService
    ): array {
        $intervalos = [];

        for ($x = $min; $x < $max; $x += $passo) {

            $x1 = $x;
            $x2 = $x + $passo;

            $fx1 = $funcaoService->avaliar($funcao, $x1);
            $fx2 = $funcaoService->avaliar($funcao, $x2);

            if ($fx1 * $fx2 < 0) {

                $intervalos[] = [
                    'a' => round($x1, 6),
                    'b' => round($x2, 6),
                    'fa' => round($fx1, 6),
                    'fb' => round($fx2, 6),
                ];
            }
        }

        return $intervalos;
    }
}
