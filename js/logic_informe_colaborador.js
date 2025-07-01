$(document).ready(function() {
    let barChartInstance = null;
    let rankingTableInstance = null;

    $('#filtro-colaborador-form').on('submit', function(e) {
        e.preventDefault();

        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();
        const colaborador = $('#colaborador').val();
        const resultadoDiv = $('#resultado-informe');
        const botonesGlobalesDiv = $('#contenedor-global-botones');

        if (!fechaInicio || !fechaFin) {
            resultadoDiv.html('<p class="alert alert-danger">Ambas fechas son obligatorias.</p>');
            return;
        }

        resultadoDiv.html('<p class="alert alert-info">Generando informe completo...</p>');
        botonesGlobalesDiv.empty(); 
        if (rankingTableInstance) {
            rankingTableInstance.destroy();
        }
        if (barChartInstance) {
            barChartInstance.destroy();
        }

        $.ajax({
            url: 'api_top_colaborador.php',
            type: 'GET',
            data: { fecha_inicio: fechaInicio, fecha_fin: fechaFin, colaborador: colaborador },
            dataType: 'json',
            success: function(response) {
                resultadoDiv.empty();

                if (!response.rankingData || response.rankingData.length === 0) {
                    resultadoDiv.html('<p class="alert alert-info">No se encontraron datos con los filtros seleccionados.</p>');
                    return;
                }

                resultadoDiv.html(`
                    <div class="seccion-consulta" id="ranking-container"></div>
                    <div class="seccion-consulta" id="chart-container"></div>
                `);

                const tabla = renderizarRanking(response.rankingData);
                const grafico = renderizarGrafico(response.chartData, colaborador);
                
                if (tabla) {
                    renderizarBotonesGlobales(tabla, grafico, fechaInicio, fechaFin);
                }
            },
            error: function() {
                resultadoDiv.html('<p class="alert alert-danger">Error de conexión.</p>');
            }
        });
    });

    /**
     * Función para RENDERIZAR LA TABLA DEL RANKING 
     */
    function renderizarRanking(rankingData) {
        rankingData.forEach((item, index) => {
            item.posicion = index + 1;
        });
        
        $('#ranking-container').html(`
            <h3>Ranking de Colaboradores</h3>
            <div class="tabla-contenedor">
                <table id="tabla-ranking" class="tabla-registro" style="width:100%">
                    <thead><tr><th>Posición</th><th>Colaborador</th><th>Total Unidades</th></tr></thead>
                </table>
            </div>
        `);

        return $('#tabla-ranking').DataTable({
            data: rankingData,
            columns: [
                { data: 'posicion' },
                { data: 'colaborador' },
                { data: 'total_unidades', render: $.fn.dataTable.render.number('.', ',', 0, '') }
            ],
            dom: 't', // Solo la tabla
            language: {
                url: 'js/i18n/es-ES.json' // Usando el archivo local
            },
            paging: false, 
            info: false, 
            searching: false
        });
  
    }

    /**
     * Función para RENDERIZAR EL GRÁFICO DE BARRAS
     */
    function renderizarGrafico(chartData, colaborador) {
        const chartContainer = $('#chart-container');
        if (!chartData || !chartData.labels || chartData.labels.length === 0) {
            chartContainer.html('<h3>Comportamiento Diario</h3><p class="alert alert-info">No hay datos para el gráfico.</p>');
            return null;
        }

        chartContainer.html(`
            <h3>Comportamiento Diario (Unidades Producidas)</h3>
            <div class="tabla-contenedor" style="padding: 15px; height: 500px; position: relative;">
                <canvas id="comportamientoChart"></canvas>
            </div>`);

        const aColores = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1'];
        chartData.datasets.forEach((dataset, index) => {
            dataset.backgroundColor = aColores[index % aColores.length];
        });

        if (barChartInstance) {
            barChartInstance.destroy();
        }
        
        const ctx = document.getElementById('comportamientoChart').getContext('2d');
        const tituloGrafico = 'Comportamiento de ' + (colaborador === 'todos' ? 'Todos los Colaboradores' : colaborador.toUpperCase());
        
        barChartInstance = new Chart(ctx, {
            type: 'bar', data: chartData,
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top' }, title: { display: true, text: tituloGrafico } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
        return barChartInstance;
    }

    /**
     * Función para RENDERIZAR LOS BOTONES GLOBALES Y ASIGNAR SU LÓGICA
     */
    function renderizarBotonesGlobales(tabla, grafico, fechaInicio, fechaFin) {
        const botonesDiv = $('#contenedor-global-botones');
        
        botonesDiv.html(`
            <div class="seccion-formulario" style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
                <span style="font-weight: bold; align-self: center;">RANKING:</span>
                <button id="descargarTabla" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                <button id="imprimirTabla" class="btn btn-info"><i class="fas fa-print"></i> Imprimir</button>
                <span style="font-weight: bold; align-self: center; margin-left: 20px;">GRÁFICO:</span>
                <button id="descargarGrafico" class="btn btn-success"><i class="fas fa-download"></i> Descargar</button>
                <button id="imprimirGrafico" class="btn btn-info"><i class="fas fa-print"></i> Imprimir</button>
            </div>
        `);

        const nitEmpresa = '900.123.456-7';
        const nombreEmpresa = 'BORDADOS DEL SUR S.A.S.';
        const rangoFechas = `Del ${fechaInicio} al ${fechaFin}`;
        const encabezadoExcel = `Empresa: ${nombreEmpresa}\nNIT: ${nitEmpresa}\nRango: ${rangoFechas}\nFecha de Generación: ${fechaReporte}`;
        const pieDePaginaExcel = `Reporte generado por: ${usuarioReporte}`;
        const encabezadoImprimir = `<div style="text-align:center;"><h3>${nombreEmpresa}</h3><p>NIT: ${nitEmpresa}<br>${rangoFechas}<br>Fecha: ${fechaReporte}</p></div>`;
        const pieDePaginaImprimir = `<p style="text-align:center; font-size:10px;">Generado por: ${usuarioReporte}</p>`;

        new $.fn.dataTable.Buttons(tabla, {
            buttons: [
                { extend: 'excelHtml5', className:'buttons-excel', title: 'Ranking de Colaboradores', messageTop: encabezadoExcel, messageBottom: pieDePaginaExcel },
                { extend: 'print', className:'buttons-print', title: 'Ranking de Colaboradores', messageTop: encabezadoImprimir, messageBottom: pieDePaginaImprimir, customize: function(win){ $(win.document.body).css('background','none').find('h1').css('text-align','center'); $(win.document.body).css('font-size','10pt').find('table').addClass('compact').css('font-size','inherit');} }
            ]
        });

        $('#descargarTabla').on('click', () => tabla.buttons('.buttons-excel').trigger());
        $('#imprimirTabla').on('click', () => tabla.buttons('.buttons-print').trigger());

        if (grafico) {
            $('#descargarGrafico').on('click', () => {
                const link = document.createElement('a');
                link.href = grafico.toBase64Image('image/png', 1);
                link.download = `Grafico_Colaboradores_${fechaInicio}_a_${fechaFin}.png`;
                link.click();
            });

            $('#imprimirGrafico').on('click', () => {
                const dataUrl = grafico.toBase64Image();
                const ventanaImpresion = window.open('', '_blank');
                ventanaImpresion.document.write(`<html><head><title>Imprimir Gráfico de Desempeño</title></head><body style="text-align:center;"><img src="${dataUrl}" onload="window.print(); setTimeout(function() { window.close(); }, 100);"></body></html>`);
                ventanaImpresion.document.close();
            });
        }
    }
});