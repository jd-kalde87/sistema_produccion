$(document).ready(function() {
    let rankingTable, monthlyTable, lineChart;

    $('#filtro-lineas-form').on('submit', function(e) {
        e.preventDefault();
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();
        const maquinaId = $('#maquina').val();
        
        const resultadoDiv = $('#resultado-informe');
        const botonesGlobalesDiv = $('#contenedor-global-botones');

        if (!fechaInicio || !fechaFin) { return; }

        resultadoDiv.html('<p class="alert alert-info">Generando informe completo...</p>');
        botonesGlobalesDiv.empty();
        if (rankingTable) rankingTable.destroy();
        if (monthlyTable) monthlyTable.destroy();
        if (lineChart) lineChart.destroy();

        $.ajax({
            url: 'api_informe_lineas.php',
            type: 'GET',
            data: { fecha_inicio: fechaInicio, fecha_fin: fechaFin, id_maquina: maquinaId },
            dataType: 'json',
            success: function(response) {
                resultadoDiv.empty();
                if (!response.rankingData || response.rankingData.length === 0) {
                    resultadoDiv.html('<p class="alert alert-info">No se encontraron datos...</p>');
                    return;
                }

                resultadoDiv.html(`
                    <div class="seccion-consulta" id="ranking-container"></div>
                    <div class="seccion-consulta" id="monthly-breakdown-container"></div>
                    <div class="seccion-consulta" id="chart-container"></div>
                `);

                rankingTable = renderRanking(response.rankingData);
                monthlyTable = renderMonthly(response.monthlyData);
                lineChart = renderLineChart(response.chartData);
                
                renderizarBotonesGlobales({rankingTable, monthlyTable, lineChart}, {fechaInicio, fechaFin});
            },
            error: function() { resultadoDiv.html('<p class="alert alert-danger">Error de conexión.</p>'); }
        });
    });

    const numberFormat = $.fn.dataTable.render.number('.', ',', 0, '');

    function renderRanking(data) {
        $('#ranking-container').html(`<h3>Ranking de Puntadas Totales</h3><div class="tabla-contenedor"><table id="tabla-ranking" class="tabla-registro" style="width:100%"><thead><tr><th>Máquina</th><th>Total Puntadas</th></tr></thead></table></div>`);
        return $('#tabla-ranking').DataTable({
            data: data,
            columns: [{ data: 'maquina_etiqueta' }, { data: 'total_puntadas', render: numberFormat }],
            dom: 't', language: { url: 'js/i18n/es-ES.json' }, paging: false, info: false, searching: false, order: [[1, 'desc']]
        });
    }

    function renderMonthly(data) {
        $('#monthly-breakdown-container').html(`<h3>Desglose de Puntadas por Mes</h3><div class="tabla-contenedor"><table id="tabla-mensual" class="tabla-registro" style="width:100%"><thead><tr><th>Máquina</th><th>Ene</th><th>Feb</th><th>Mar</th><th>Abr</th><th>May</th><th>Jun</th><th>Jul</th><th>Ago</th><th>Sep</th><th>Oct</th><th>Nov</th><th>Dic</th></tr></thead></table></div>`);
        return $('#tabla-mensual').DataTable({
            data: data,
            columns: [
                { data: 'maquina_etiqueta' }, { data: 'Ene', render: numberFormat }, { data: 'Feb', render: numberFormat }, { data: 'Mar', render: numberFormat },
                { data: 'Abr', render: numberFormat }, { data: 'May', render: numberFormat }, { data: 'Jun', render: numberFormat },
                { data: 'Jul', render: numberFormat }, { data: 'Ago', render: numberFormat }, { data: 'Sep', render: numberFormat },
                { data: 'Oct', render: numberFormat }, { data: 'Nov', render: numberFormat }, { data: 'Dic', render: numberFormat }
            ],
            dom: 't', language: { url: 'js/i18n/es-ES.json' }, paging: false, info: false, searching: false
        });
    }

    function renderLineChart(chartData) {
        if (!chartData || !chartData.labels.length === 0) { $('#chart-container').html('<h3>Gráfico de Tendencias</h3><p class="alert alert-info">No hay datos para el gráfico.</p>'); return null; }
        $('#chart-container').html(`<div class="tabla-contenedor" style="padding:15px; height:500px;"><canvas id="tendenciasChart"></canvas></div>`);
        
        const aColores = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1'];
        chartData.datasets.forEach((d, i) => { d.borderColor = aColores[i % aColores.length]; d.fill = false; d.tension = 0.1; });
        
        return new Chart(document.getElementById('tendenciasChart').getContext('2d'), {
            type: 'line', 
            data: chartData, 
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { position: 'top' },
                    // ===============================================
                    // ===== CAMBIO 3: AÑADIR TÍTULO AL GRÁFICO =====
                    // ===============================================
                    title: {
                        display: true,
                        text: 'Tendencia de Puntadas por Máquina',
                        font: { size: 18 }
                    }
                }, 
                scales: { y: { beginAtZero: true } } 
            }
        });
    }

    function renderizarBotonesGlobales(elementos, fechas) {
        const botonesDiv = $('#contenedor-global-botones');
        botonesDiv.html(`
            <div class="seccion-formulario" style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap; align-items: center;">
                <span style="font-weight: bold;">RANKING:</span>
                <button id="dlRanking" class="btn btn-success"><i class="fas fa-file-excel"></i></button>
                <button id="prRanking" class="btn btn-info"><i class="fas fa-print"></i></button>
                <span style="font-weight: bold; margin-left: 15px;">DESGLOSE MENSUAL:</span>
                <button id="dlMensual" class="btn btn-success"><i class="fas fa-file-excel"></i></button>
                <button id="prMensual" class="btn btn-info"><i class="fas fa-print"></i></button>
                <span style="font-weight: bold; margin-left: 15px;">GRÁFICO:</span>
                <button id="dlGrafico" class="btn btn-success"><i class="fas fa-download"></i></button>
                <button id="prGrafico" class="btn btn-info"><i class="fas fa-print"></i></button>
            </div>
        `);

        const nitEmpresa = '900.123.456-7', nombreEmpresa = 'BORDADOS DEL SUR S.A.S.';
        const rangoFechas = `Del ${fechas.fechaInicio} al ${fechas.fechaFin}`;
        const pieDePagina = `Reporte generado por: ${usuarioReporte}`;
        const encabezadoExcel = `Empresa: ${nombreEmpresa}\nNIT: ${nitEmpresa}\nRango: ${rangoFechas}\nFecha Generación: ${fechaReporte}`;
        const encabezadoImprimir = `<div style="text-align:center;"><h3>${nombreEmpresa}</h3><p>NIT: ${nitEmpresa}<br>${rangoFechas}<br>Fecha: ${fechaReporte}</p></div>`;

        const setupTableButtons = (tableInstance, baseName, downloadBtnId, printBtnId) => {
            if (!tableInstance) return;
            new $.fn.dataTable.Buttons(tableInstance, { 
                buttons: [ 
                    { extend: 'excelHtml5', className:'buttons-excel', title: baseName, messageTop: encabezadoExcel, messageBottom: pieDePagina },
                    { 
                        extend: 'print', 
                        className:'buttons-print', 
                        title: baseName, 
                        messageTop: encabezadoImprimir, 
                        messageBottom: pieDePagina, 
                        // ==========================================================
                        // ===== CAMBIO 1: FONDO BLANCO AL IMPRIMIR TABLAS =====
                        // ==========================================================
                        customize: function(win) {
                            $(win.document.body).css('background', 'none'); // <-- AQUÍ
                            $(win.document.body).find('h1').css('text-align', 'center');
                            $(win.document.body).css('font-size', '10pt').find('table').addClass('compact').css('font-size', 'inherit');
                        } 
                    } 
                ]
            });
            $(`#${downloadBtnId}`).on('click', () => tableInstance.buttons('.buttons-excel').trigger());
            $(`#${printBtnId}`).on('click', () => tableInstance.buttons('.buttons-print').trigger());
        };
        
        setupTableButtons(elementos.rankingTable, 'Ranking de Puntadas por Máquina', 'dlRanking', 'prRanking');
        setupTableButtons(elementos.monthlyTable, 'Desglose Mensual de Puntadas', 'dlMensual', 'prMensual');

        if (elementos.lineChart) {
            $('#dlGrafico').on('click', () => {
                const link = document.createElement('a');
                link.href = elementos.lineChart.toBase64Image();
                link.download = `Grafico_Tendencias_${fechas.fechaInicio}_a_${fechas.fechaFin}.png`;
                link.click();
            });

            $('#prGrafico').on('click', () => {
                const dataUrl = elementos.lineChart.toBase64Image();
                const ventanaImpresion = window.open('', '_blank');
                // =================================================================================================
                // ===== CAMBIO 2: MEJORAR ESTRUCTURA HTML PARA IMPRIMIR GRÁFICO (EVITA ERRORES Y MEJORA POSICIÓN) =====
                // =================================================================================================
                ventanaImpresion.document.write(`
                    <html>
                        <head><title>Imprimir Gráfico de Tendencias</title></head>
                        <body style="font-family: Arial, sans-serif;">
                            ${encabezadoImprimir}
                            <div style="text-align:center; margin-top: 20px;">
                                <img src="${dataUrl}" style="max-width: 95%;" onload="window.print(); setTimeout(function(){window.close();}, 100);">
                            </div>
                            <p style="text-align:center; font-size:10px; margin-top:15px;">${pieDePagina}</p>
                        </body>
                    </html>`);
                ventanaImpresion.document.close();
            });
        }
    }
});