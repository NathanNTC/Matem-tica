<?php

namespace App\Services;

use NXP\MathExecutor;

class FuncaoService
{
    public function avaliar(string $funcao, float $x): float
    {
        $executor = new MathExecutor();

        $executor->setVar('x', $x);

        return $executor->execute($funcao);
    }
}