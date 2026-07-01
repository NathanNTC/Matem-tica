<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricoCalculo;
use App\Services\FuncaoService;
use App\Services\BuscaRaizesService;
use App\Services\BissecaoService;
use App\Services\NewtonRaphsonService;

class MetodoController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function recalcular(Request $request)
    {
        return $this->calcular(
            $request,
            app(FuncaoService::class),
            app(BuscaRaizesService::class),
            app(BissecaoService::class),
            app(NewtonRaphsonService::class),
        );
    }

    public function historico()
    {
        $historicos = HistoricoCalculo::latest()->get();
        return view('historico', compact('historicos'));
    }

    public function dadosHistorico($id)
    {
        $historico = HistoricoCalculo::findOrFail($id);

        return response()->json([
            'funcao' => $historico->funcao,
            'min' => $historico->min,
            'max' => $historico->max,
            'passo' => $historico->passo,
            'tolerancia' => $historico->tolerancia,
            'intervalos' => json_decode($historico->intervalos, true),
            'bissecao' => json_decode($historico->resultado_bissecao, true),
            'newton' => json_decode($historico->resultado_newton, true),
        ]);
    }

    public function calcular(
        Request $request,
        FuncaoService $funcaoService,
        BuscaRaizesService $buscaRaizesService,
        BissecaoService $bissecaoService,
        NewtonRaphsonService $newtonRaphsonService
    ) {
        $funcao = $request->funcao;

        $intervalos = $buscaRaizesService->buscarIntervalos(
            $funcao,
            (float) $request->min,
            (float) $request->max,
            (float) $request->passo,
            $funcaoService
        );

        if (empty($intervalos)) {
            return view('home', [
                'funcao' => $funcao,
                'intervalos' => []
            ]);
        }

        $resultadoBissecao = $bissecaoService->calcular(
            $funcao,
            $intervalos[0]['a'],
            $intervalos[0]['b'],
            (float) $request->tolerancia,
            $funcaoService
        );

        $resultadoNewton = $newtonRaphsonService->calcular(
            $funcao,
            ($intervalos[0]['a'] + $intervalos[0]['b']) / 2,
            (float) $request->tolerancia,
            $funcaoService
        );

        HistoricoCalculo::create([
            'funcao' => $funcao,
            'min' => (float) $request->min,
            'max' => (float) $request->max,
            'passo' => (float) $request->passo,
            'tolerancia' => (float) $request->tolerancia,
            'intervalos' => json_encode($intervalos),
            'resultado_bissecao' => json_encode($resultadoBissecao),
            'resultado_newton' => json_encode($resultadoNewton),
        ]);

        return view('home', [
            'funcao' => $funcao,
            'intervalos' => $intervalos,
            'resultadoBissecao' => $resultadoBissecao,
            'resultadoNewton' => $resultadoNewton,
        ]);
    }
}