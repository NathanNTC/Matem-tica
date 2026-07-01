<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Métodos Numéricos</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- GEO GEBRA -->
    <script src="https://www.geogebra.org/apps/deployggb.js"></script>
</head>

<style>
    body {
        background-color: #121212;
        color: #e0e0e0;
    }

    .container {
        background-color: #1e1e1e;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 0 20px rgba(0,0,0,0.4);
    }

    .form-control {
        background-color: #2b2b2b;
        border: 1px solid #444;
        color: #fff;
    }

    .form-control:focus {
        background-color: #333;
        color: #fff;
        border-color: #0d6efd;
        box-shadow: none;
    }

    .form-control::placeholder {
        color: #aaa;
    }

    .form-label {
        color: #ddd;
    }

    .table {
        color: #e0e0e0;
    }

    .table-bordered {
        border-color: #444;
    }

    .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: rgba(255,255,255,0.03);
    }

    .table-dark {
        --bs-table-bg: #0d1117;
    }

    .alert-success {
        background-color: #0f5132;
        border-color: #146c43;
        color: #d1e7dd;
    }

    .alert-warning {
        background-color: #664d03;
        border-color: #997404;
        color: #fff3cd;
    }

    .alert-info {
        background-color: #055160;
        border-color: #087990;
        color: #cff4fc;
    }

    h1, h2, h3 {
        color: #ffffff;
    }

    .btn-primary {
        font-weight: bold;
    }

    hr {
        border-color: #444;
    }
</style>

<body>

<a href="/historico" class="btn btn-secondary mt-4">
    Histórico
</a>

<div class="container mt-5">

    <h1 class="mb-4">Comparador de Métodos Numéricos</h1>

    <form method="POST" action="/calcular">

        @csrf

        <div class="mb-3">
            <label class="form-label">
                Função
            </label>

            <input
                type="text"
                class="form-control"
                name="funcao"
                placeholder="x^3 - 6*x^2 + 11*x - 6"
                value="{{ old('funcao') }}">
        </div>

        <div class="row">

            <div class="col-md-4">
                <label class="form-label">
                    Limite Inferior
                </label>

                <input
                    type="number"
                    step="any"
                    class="form-control"
                    name="min"
                    value="{{ old('min', -100) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    Limite Superior
                </label>

                <input
                    type="number"
                    step="any"
                    class="form-control"
                    name="max"
                    value="{{ old('max', 100) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    Passo
                </label>

                <input
                    type="number"
                    step="any"
                    class="form-control"
                    name="passo"
                    value="{{ old('passo', 0.1) }}">
            </div>

        </div>

        <div class="mt-3">

            <label class="form-label">
                Tolerância
            </label>

            <input
                type="number"
                step="any"
                class="form-control"
                name="tolerancia"
                value="{{ old('tolerancia', 0.0001) }}">
        </div>

        <button class="btn btn-primary mt-4" type="submit">
            Calcular
        </button>

    </form>

    {{-- INTERVALOS ENCONTRADOS --}}
    @if(isset($intervalos))

        <div class="mt-5">

            <h3>Intervalos Encontrados</h3>

            @if(count($intervalos))

                <table class="table table-bordered table-striped">

                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>A</th>
                            <th>B</th>
                            <th>f(A)</th>
                            <th>f(B)</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($intervalos as $index => $intervalo)

                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $intervalo['a'] }}</td>
                                <td>{{ $intervalo['b'] }}</td>
                                <td>{{ $intervalo['fa'] }}</td>
                                <td>{{ $intervalo['fb'] }}</td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

                <div class="alert alert-success">
                    Total de intervalos encontrados:
                    <strong>{{ count($intervalos) }}</strong>
                </div>

            @else

                <div class="alert alert-warning">
                    Nenhum intervalo com mudança de sinal foi encontrado.
                </div>

            @endif

        </div>

    @endif

    {{-- BISSEÇÃO --}}
    @if(isset($resultadoBissecao))

        <div class="mt-5">

            <h2>Método da Bisseção</h2>

            <div class="alert alert-info">

                <strong>Raiz encontrada:</strong>
                {{ number_format($resultadoBissecao['raiz'], 8) }}

                <br>

                <strong>Total de iterações:</strong>
                {{ $resultadoBissecao['total_iteracoes'] }}

            </div>

            <table class="table table-bordered table-striped">

                <thead class="table-dark">
                    <tr>
                        <th>Iteração</th>
                        <th>A</th>
                        <th>B</th>
                        <th>Xm</th>
                        <th>f(A)</th>
                        <th>f(B)</th>
                        <th>f(Xm)</th>
                        <th>Erro</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($resultadoBissecao['iteracoes'] as $linha)

                        <tr>
                            <td>{{ $linha['iteracao'] }}</td>
                            <td>{{ number_format($linha['a'], 8) }}</td>
                            <td>{{ number_format($linha['b'], 8) }}</td>
                            <td>{{ number_format($linha['xm'], 8) }}</td>
                            <td>{{ number_format($linha['fa'], 8) }}</td>
                            <td>{{ number_format($linha['fb'], 8) }}</td>
                            <td>{{ number_format($linha['fxm'], 8) }}</td>
                            <td>{{ number_format($linha['erro'], 8) }}</td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif

    {{-- NEWTON-RAPHSON --}}
    @if(isset($resultadoNewton))

        <div class="mt-5">

            <h2>Método de Newton-Raphson</h2>

            <div class="alert alert-warning">

                <strong>Raiz encontrada:</strong>
                {{ number_format($resultadoNewton['raiz'], 8) }}

                <br>

                <strong>Total de iterações:</strong>
                {{ $resultadoNewton['total_iteracoes'] }}

            </div>

            <table class="table table-bordered table-striped">

                <thead class="table-dark">
                    <tr>
                        <th>Iteração</th>
                        <th>Xn</th>
                        <th>f(Xn)</th>
                        <th>f'(Xn)</th>
                        <th>Xn+1</th>
                        <th>Erro</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($resultadoNewton['iteracoes'] as $linha)

                        <tr>
                            <td>{{ $linha['iteracao'] }}</td>
                            <td>{{ number_format($linha['xn'], 8) }}</td>
                            <td>{{ number_format($linha['fx'], 8) }}</td>
                            <td>{{ number_format($linha['derivada'], 8) }}</td>
                            <td>{{ number_format($linha['xn1'], 8) }}</td>
                            <td>{{ number_format($linha['erro'], 8) }}</td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif

</div>

{{-- ================= GEO GEBRA (ADICIONADO SEM MEXER EM NADA) ================= --}}
<div class="container">

    <h3>Gráfico (GeoGebra)</h3>

    <div id="ggb-element" style="width:100%; height:500px;"></div>

</div>

<script>
let ggbApp;
let ggbApi = null;

window.addEventListener("load", function () {

    const funcao = @json($funcao ?? 'x^3 - 6*x^2 + 11*x - 6');

    const params = {
        appName: "graphing",
        width: 1100,
        height: 500,
        showToolBar: true,
        showAlgebraInput: true,
        showMenuBar: false,
        appletOnLoad: function(api) {

            ggbApi = api;

            // função do usuário
            ggbApi.evalCommand(`f(x) = ${funcao}`);

            @if(isset($resultadoBissecao))
                ggbApi.evalCommand(`Bissecao = ({{ $resultadoBissecao['raiz'] }}, 0)`);
            @endif

            @if(isset($resultadoNewton))
                ggbApi.evalCommand(`Newton = ({{ $resultadoNewton['raiz'] }}, 0)`);
            @endif

        }
    };

    ggbApp = new GGBApplet(params, true);
    ggbApp.inject("ggb-element");
});
</script>

</body>
</html>