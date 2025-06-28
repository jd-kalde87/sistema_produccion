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
        if (rankingTableInstance) rankingTableInstance.destroy();
        if (barChartInstance) barChartInstance.destroy();

        $.ajax({
            url: 'api_top_colaborador.php',
            type: 'GET',
            data: { fecha_inicio: fechaInicio, fecha_fin: fechaFin, colaborador: colaborador },
            dataType: 'json',
            success: function(response) {
                resultadoDiv.empty();

                if (!response.rankingData || response.rankingData.length === 0) {
                    resultadoDiv.html('<p class="alert alert-info">No se encontraron datos...</p>');
                    return;
                }

                resultadoDiv.html(`
                    <div class="seccion-consulta" id="ranking-container"></div>
                    <div class="seccion-consulta" id="chart-container"></div>
                `);

                const tabla = renderizarRanking(response.rankingData);
                const grafico = renderizarGrafico(response.chartData, colaborador);

                if(tabla && grafico) {
                    // Pasamos las variables de fecha al generador de botones
                    renderizarBotonesGlobales(tabla, grafico, fechaInicio, fechaFin);
                }
            },
            error: function() {
                resultadoDiv.html('<p class="alert alert-danger">Error de conexión.</p>');
            }
        });
    });

    function renderizarRanking(rankingData) {
        // Esta función no cambia
        if (rankingTableInstance) { rankingTableInstance.destroy(); }
        rankingData.forEach((item, index) => { item.posicion = index + 1; });
        
        $('#ranking-container').html(`
            <h3>Ranking de Colaboradores</h3>
            <div id="controles-tabla-ranking" style="display: flex; justify-content: flex-end; margin-bottom: 20px;"></div>
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
            dom: 't', // DOM mínimo, solo la tabla, ya que los controles son externos
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
            paging: false, info: false, searching: false
        });
    }

    /**
     * Se añade el parámetro 'colaborador' para usarlo en el título del gráfico.
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

        if (barChartInstance) barChartInstance.destroy();

        const ctx = document.getElementById('comportamientoChart').getContext('2d');
        const tituloGrafico = 'Comportamiento de ' + (colaborador === 'todos' ? 'Todos los Colaboradores' : colaborador.toUpperCase());

        return new Chart(ctx, {
            type: 'bar', data: chartData,
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'top' },
                    // Título dinámico para el gráfico
                    title: { display: true, text: tituloGrafico }
                },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    /**
     * Función para RENDERIZAR LOS BOTONES GLOBALES
     * Actualizada con la nueva lógica para descargar e imprimir el gráfico.
     */
    function renderizarBotonesGlobales(tabla, grafico, fechaInicio, fechaFin) {
        const botonesDiv = $('#contenedor-global-botones');
        
        // HTML de los 4 botones
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

        // --- LÓGICA PARA LOS BOTONES DEL RANKING (TABLA) ---
        const nitEmpresa = '900.123.456-7';
        const nombreEmpresa = 'BORDADOS DEL SUR S.A.S.';
        const rangoFechas = `Del ${fechaInicio} al ${fechaFin}`;
        const encabezadoExcel = `Empresa: ${nombreEmpresa}\nNIT: ${nitEmpresa}\nRango: ${rangoFechas}\nFecha de Generación: ${fechaReporte}`;
        const pieDePaginaExcel = `Reporte generado por: ${usuarioReporte}`;
        const encabezadoImprimir = `<div style="text-align:center;"><h3>${nombreEmpresa}</h3><p>NIT: ${nitEmpresa}<br>${rangoFechas}<br>Fecha: ${fechaReporte}</p></div>`;
        const pieDePaginaImprimir = `<p style="text-align:center; font-size:10px;">Generado por: ${usuarioReporte}</p>`;

        const botonesTabla = new $.fn.dataTable.Buttons(tabla, {
            buttons: [
                { extend: 'excelHtml5', title: 'Ranking de Colaboradores', messageTop: encabezadoExcel, messageBottom: pieDePaginaExcel },
                { extend: 'print', title: 'Ranking de Colaboradores', messageTop: encabezadoImprimir, messageBottom: pieDePaginaImprimir, customize: function(win){ $(win.document.body).css('background','none').find('h1').css('text-align','center'); $(win.document.body).css('font-size','10pt').find('table').addClass('compact').css('font-size','inherit');} }
            ]
        }).container().hide();

        $('#descargarTabla').on('click', () => botonesTabla.find('.buttons-excel').trigger('click'));
        $('#imprimirTabla').on('click', () => botonesTabla.find('.buttons-print').trigger('click'));

        // ===============================================
        // ===== INICIO DE LA MODIFICACIÓN =====
        // ===============================================

        // --- LÓGICA PARA LOS BOTONES DEL GRÁFICO (ACTUALIZADA) ---
        $('#descargarGrafico').on('click', () => {
            const link = document.createElement('a');
            link.href = grafico.toBase64Image('image/png', 1);
            // Nombre de archivo dinámico
            link.download = `Grafico_Colaboradores_${fechaInicio}_a_${fechaFin}.png`;
            link.click();
        });
        
        // CORRECCIÓN para la impresión en blanco
        $('#imprimirGrafico').on('click', () => {
            const dataUrl = grafico.toBase64Image();
            const ventanaImpresion = window.open('', '_blank');
            
            // Construimos el HTML completo con encabezado, imagen y pie de página
            ventanaImpresion.document.write(`
                <html>
                    <head><title>Imprimir Gráfico de Desempeño</title></head>
                    <body style="font-family: Arial, sans-serif;">
                        ${encabezadoImprimir}
                        <div style="text-align:center; margin-top: 20px;">
                            <img src="${dataUrl}" style="max-width: 95%;" onload="window.print(); setTimeout(function() { window.close(); }, 100);">
                        </div>
                        ${pieDePaginaImprimir}
                    </body>
                </html>`);
            ventanaImpresion.document.close();
        });
        // ===============================================
        // ===== FIN DE LA MODIFICACIÓN =====
        // ===============================================
    }
});