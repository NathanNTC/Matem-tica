<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://www.geogebra.org/apps/deployggb.js"></script>
</head>

<body class="bg-dark text-white">

<div class="container mt-5">

    <h1 class="mb-4">Histórico de Cálculos</h1>

    <a href="/" class="btn btn-primary mb-3">Voltar</a>

    <table class="table table-dark table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Função</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($historicos as $h)
                <tr>
                    <td>{{ $h->id }}</td>
                    <td>{{ $h->funcao }}</td>
                    <td>{{ $h->created_at }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="abrirModal({{ $h->id }})">
                            Ver
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

<div class="modal fade" id="modalHistorico" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content bg-dark text-white">

            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Cálculo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

               <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3" id="infoCalculo"></div>

                        <button class="btn btn-secondary btn-sm mb-3 w-100"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#iteracoesCollapse">
                            Expandir iterações
                        </button>
                    </div>
                </div>

                
                <div class="row mt-3">
                    <div class="col-12">
                        <h5 class="text-success">Gráfico da Função (GeoGebra)</h5>

                        <div id="geogebra-container"
                            style="border:1px solid #444;border-radius:6px;height:350px;">
                        </div>
                    </div>
                </div>

                    <div class="col-md-8 mb-4">
                        <h5 class="text-success">Gráfico da Função (GeoGebra)</h5>
                        <div id="geogebra-container"
                             style="border:1px solid #444;border-radius:6px;height:350px;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-info">Bisseção</h5>
                        <canvas id="graficoBissecao"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-warning">Newton-Raphson</h5>
                        <canvas id="graficoNewton"></canvas>
                    </div>
                </div>

                <div class="collapse mt-4" id="iteracoesCollapse">

                    <h5 class="mt-3">Iterações Bisseção</h5>
                    <table class="table table-dark table-sm table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>A</th>
                                <th>B</th>
                                <th>Xm</th>
                                <th>f(Xm)</th>
                                <th>Erro</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyBissecao"></tbody>
                    </table>

                    <h5 class="mt-4">Iterações Newton</h5>
                    <table class="table table-dark table-sm table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Xn</th>
                                <th>f(Xn)</th>
                                <th>f'(Xn)</th>
                                <th>Xn+1</th>
                                <th>Erro</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyNewton"></tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

let chartB = null;
let chartN = null;
let ggbApp = null;

const modalEl = document.getElementById('modalHistorico');
const modal = new bootstrap.Modal(modalEl);

function formatarFuncaoGeoGebra(funcao) {
    return funcao
        .replaceAll("^", "**")
        .replaceAll(" ", "");
}

function renderGeoGebra(funcao) {

    const container = document.getElementById("geogebra-container");
    container.innerHTML = "";

    let funcaoFormatada = formatarFuncaoGeoGebra(funcao);

    ggbApp = new GGBApplet({
        width: 800,
        height: 350,
        showToolBar: false,
        showAlgebraInput: false,
        showMenuBar: false,
        showResetIcon: false,
        enableRightClick: false,
        appletOnLoad: function(api) {
            api.evalCommand(`f(x) = ${funcaoFormatada}`);
            api.setColor("f", 0, 200, 100);
        }
    }, true);

    ggbApp.inject("geogebra-container");
}

function abrirModal(id) {

    fetch(`/historico-dados/${id}`)
        .then(res => res.json())
        .then(data => {

            document.getElementById("infoCalculo").innerHTML = `
                <p><b>Função:</b> ${data.funcao}</p>
                <p><b>Intervalo:</b> [${data.min}, ${data.max}]</p>
                <p><b>Passo:</b> ${data.passo}</p>
                <p><b>Tolerância:</b> ${data.tolerancia}</p>
            `;

            const bissecao = data.bissecao.iteracoes;
            const newton = data.newton.iteracoes;

            if (chartB) chartB.destroy();
            if (chartN) chartN.destroy();

            chartB = new Chart(document.getElementById('graficoBissecao'), {
                type: 'line',
                data: {
                    labels: bissecao.map((_, i) => i),
                    datasets: [{
                        label: 'Xm',
                        data: bissecao.map(v => v.xm),
                        borderColor: 'cyan'
                    }]
                }
            });

            chartN = new Chart(document.getElementById('graficoNewton'), {
                type: 'line',
                data: {
                    labels: newton.map((_, i) => i),
                    datasets: [{
                        label: 'Xn',
                        data: newton.map(v => v.xn),
                        borderColor: 'orange'
                    }]
                }
            });

            document.getElementById("tbodyBissecao").innerHTML =
                bissecao.map((v, i) => `
                    <tr>
                        <td>${i}</td>
                        <td>${v.a}</td>
                        <td>${v.b}</td>
                        <td>${v.xm}</td>
                        <td>${v.fxm}</td>
                        <td>${v.erro}</td>
                    </tr>
                `).join("");

            document.getElementById("tbodyNewton").innerHTML =
                newton.map((v, i) => `
                    <tr>
                        <td>${i}</td>
                        <td>${v.xn}</td>
                        <td>${v.fx}</td>
                        <td>${v.derivada}</td>
                        <td>${v.xn1}</td>
                        <td>${v.erro}</td>
                    </tr>
                `).join("");

            // 🔥 IMPORTANTE: renderiza GeoGebra só quando modal abrir
            modalEl.addEventListener('shown.bs.modal', function handler() {
                renderGeoGebra(data.funcao);
            }, { once: true });

            modal.show();
        });
}

</script>

</body>
</html>