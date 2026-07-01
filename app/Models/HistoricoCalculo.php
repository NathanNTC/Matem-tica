<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoCalculo extends Model
{
    protected $fillable = [
        'funcao',
        'min',
        'max',
        'passo',
        'tolerancia',
        'intervalos',
        'resultado_bissecao',
        'resultado_newton',
    ];
}