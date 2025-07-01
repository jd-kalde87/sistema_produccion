$(document).ready(function() {
    // Instancias para los 4 elementos
    let unidadesTableInstance, puntadasTableInstance, unidadesChartInstance, puntadasChartInstance;

    $('#filtro-maquinaria-form').on('submit', function(e) {
        e.preventDefault();

        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();
        const maquinaId = $('#maquina').val();
        const resultadoDiv = $('#resultado-informe');
        const botonesGlobalesDiv = $('#contenedor-global-botones');

        if (!fechaInicio || !fechaFin) {
            resultadoDiv.html('<p class="alert alert-danger">Ambas fechas son obligatorias.</p>');
            return;
        }

        // Limpieza completa antes de generar el nuevo informe
        resultadoDiv.html('<p class="alert alert-info">Generando informe completo...</p>');
        botonesGlobalesDiv.empty();
        if (unidadesTableInstance) unidadesTableInstance.destroy();
        if (puntadasTableInstance) puntadasTableInstance.destroy();
        if (unidadesChartInstance) unidadesChartInstance.destroy();
        if (puntadasChartInstance) puntadasChartInstance.destroy();

        $.ajax({
            url: 'api_informe_maquinaria.php',
            type: 'GET',
            data: { fecha_inicio: fechaInicio, fecha_fin: fechaFin, id_maquina: maquinaId },
            dataType: 'json',
            success: function(response) {
                resultadoDiv.empty();

                if ((!response.unidadesData || response.unidadesData.length === 0) && (!response.puntadasData || response.puntadasData.length === 0)) {
                    resultadoDiv.html('<p class="alert alert-info">No se encontraron datos con los filtros seleccionados.</p>');
                    return;
                }

                resultadoDiv.html(`
                    <div class="seccion-consulta" id="ranking-unidades-container"></div>
                    <div class="seccion-consulta" id="chart-unidades-container"></div>
                    <div class="seccion-consulta" id="ranking-puntadas-container"></div>
                    <div class="seccion-consulta" id="chart-puntadas-container"></div>
                `);

                const tablaUnidades = renderizarRankingUnidades(response.unidadesData);
                const tablaPuntadas = renderizarRankingPuntadas(response.puntadasData);
                const graficoUnidades = renderizarGraficoUnidades(response.unidadesData);
                const graficoPuntadas = renderizarGraficoPuntadas(response.puntadasData);

                renderizarBotonesGlobales({ tablaUnidades, tablaPuntadas, graficoUnidades, graficoPuntadas }, { fechaInicio, fechaFin });
            },
            error: function() {
                resultadoDiv.html('<p class="alert alert-danger">Error de conexión.</p>');
            }
        });
    });

    // ==========================================================
    // ===== INICIO DE LA CORRECCIÓN =====
    // ==========================================================

    /**
     * Función COMPLETA para renderizar el ranking de Unidades
     */
    function renderizarRankingUnidades(data) {
        if (!data || data.length === 0) {
            $('#ranking-unidades-container').html('<h3>Ranking de Unidades</h3><p class="alert alert-info">No hay datos para este ranking.</p>');
            return null;
        }
        $('#ranking-unidades-container').html(`<h3>Ranking de Unidades Producidas</h3><div class="tabla-contenedor"><table id="tabla-ranking-unidades" class="tabla-registro" style="width:100%"><thead><tr><th>Máquina</th><th>Total Unidades</th></tr></thead></table></div>`);
        return $('#tabla-ranking-unidades').DataTable({
            data: data,
            columns: [{ data: 'maquina_etiqueta' }, { data: 'total_unidades', render: $.fn.dataTable.render.number('.', ',', 0, '') }],
            dom: 't',
            language: { url: 'js/i18n/es-ES.json' },
            paging: false,
            info: false,
            searching: false,
            order: [[1, 'desc']]
        });
    }

    /**
     * Función COMPLETA para renderizar el ranking de Puntadas
     */
    function renderizarRankingPuntadas(data) {
        if (!data || data.length === 0) {
            $('#ranking-puntadas-container').html('<h3>Ranking de Puntadas</h3><p class="alert alert-info">No hay datos para este ranking.</p>');
            return null;
        }
        $('#ranking-puntadas-container').html(`<h3>Ranking de Puntadas Totales</h3><div class="tabla-contenedor"><table id="tabla-ranking-puntadas" class="tabla-registro" style="width:100%"><thead><tr><th>Máquina</th><th>Total Puntadas</th></tr></thead></table></div>`);
        return $('#tabla-ranking-puntadas').DataTable({
            data: data,
            columns: [{ data: 'maquina_etiqueta' }, { data: 'total_puntadas', render: $.fn.dataTable.render.number('.', ',', 0, '') }],
            dom: 't',
            language: { url: 'js/i18n/es-ES.json' },
            paging: false,
            info: false,
            searching: false,
            order: [[1, 'desc']]
        });
    }

    /**
     * Función COMPLETA para renderizar el gráfico de Unidades (con título)
     */
    function renderizarGraficoUnidades(data) {
        if (!data || data.length === 0) {
            $('#chart-unidades-container').html('<h3>Gráfico de Unidades</h3><p class="alert alert-info">No hay datos para este gráfico.</p>');
            return null;
        }
        $('#chart-unidades-container').html(`<div class="tabla-contenedor" style="padding:15px; height:400px;"><canvas id="unidadesChart"></canvas></div>`);
        unidadesChartInstance = new Chart(document.getElementById('unidadesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.map(item => item.maquina_etiqueta),
                datasets: [{ label: 'Total Unidades', data: data.map(item => item.total_unidades), backgroundColor: 'rgba(40, 167, 69, 0.6)' }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Unidades Producidas por Máquina', font: { size: 16 } }
                },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
        return unidadesChartInstance;
    }

    /**
     * Función COMPLETA para renderizar el gráfico de Puntadas (con título)
     */
    function renderizarGraficoPuntadas(data) {
        if (!data || data.length === 0) {
            $('#chart-puntadas-container').html('<h3>Gráfico de Puntadas</h3><p class="alert alert-info">No hay datos para este gráfico.</p>');
            return null;
        }
        $('#chart-puntadas-container').html(`<div class="tabla-contenedor" style="padding:15px; height:400px;"><canvas id="puntadasChart"></canvas></div>`);
        puntadasChartInstance = new Chart(document.getElementById('puntadasChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.map(item => item.maquina_etiqueta),
                datasets: [{ label: 'Total Puntadas', data: data.map(item => item.total_puntadas), backgroundColor: 'rgba(0, 123, 255, 0.6)' }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Puntadas Totales por Máquina', font: { size: 16 } }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
        return puntadasChartInstance;
    }
    
    /**
     * Función COMPLETA para renderizar los botones globales
     */
    function renderizarBotonesGlobales(elementos, fechas) {
        const botonesDiv = $('#contenedor-global-botones');
        botonesDiv.html(`
            <div class="seccion-formulario" style="display: flex; gap: 10px; justify-content: flex-start; flex-wrap: wrap; align-items: center;">
                <span style="font-weight: bold;">RANKING UNIDADES:</span>
                <button id="dlUnidades" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                <button id="prUnidades" class="btn btn-info"><i class="fas fa-print"></i> Imprimir</button>
                <span style="font-weight: bold; margin-left: 10px;">RANKING PUNTADAS:</span>
                <button id="dlPuntadas" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                <button id="prPuntadas" class="btn btn-info"><i class="fas fa-print"></i> Imprimir</button>
            </div>
            <div class="seccion-formulario" style="display: flex; gap: 10px; justify-content: flex-start; flex-wrap: wrap; align-items: center;">
                <span style="font-weight: bold;">GRÁFICO UNIDADES:</span>
                <button id="dlGraficoUnidades" class="btn btn-success"><i class="fas fa-download"></i> Descargar PNG</button>
                <button id="prGraficoUnidades" class="btn btn-info"><i class="fas fa-print"></i> Imprimir</button>
                <span style="font-weight: bold; margin-left: 10px;">GRÁFICO PUNTADAS:</span>
                <button id="dlGraficoPuntadas" class="btn btn-success"><i class="fas fa-download"></i> Descargar PNG</button>
                <button id="prGraficoPuntadas" class="btn btn-info"><i class="fas fa-print"></i> Imprimir</button>
            </div>
            `);

        const nitEmpresa = '900.123.456-7';
        const nombreEmpresa = 'BORDADOS DEL SUR S.A.S.';
        const rangoFechas = `Del ${fechas.fechaInicio} al ${fechas.fechaFin}`;
        const pieDePagina = `Reporte generado por: ${usuarioReporte}`;
        const encabezadoExcel = `Empresa: ${nombreEmpresa}\nNIT: ${nitEmpresa}\nRango: ${rangoFechas}\nFecha Generación: ${fechaReporte}`;
        const encabezadoImprimir = `<div style="text-align:center;font-family:Arial,sans-serif;"><h3>${nombreEmpresa}</h3><p>NIT: ${nitEmpresa}<br>${rangoFechas}<br>Fecha: ${fechaReporte}</p></div>`;
        
        if (elementos.tablaUnidades) {
            new $.fn.dataTable.Buttons(elementos.tablaUnidades, { buttons: [ { extend: 'excelHtml5', className:'buttons-excel', title: 'Ranking de Unidades por Máquina', messageTop: encabezadoExcel, messageBottom: pieDePagina }, { extend: 'print', className:'buttons-print', title: 'Ranking de Unidades por Máquina', messageTop: encabezadoImprimir, messageBottom: pieDePagina, customize: function(win){$(win.document.body).css('background','none').find('h1').css('text-align','center');$(win.document.body).css('font-size','10pt').find('table').addClass('compact').css('font-size','inherit');} } ] });
            $('#dlUnidades').on('click', () => elementos.tablaUnidades.buttons('.buttons-excel').trigger());
            $('#prUnidades').on('click', () => elementos.tablaUnidades.buttons('.buttons-print').trigger());
        }

        if (elementos.tablaPuntadas) {
            new $.fn.dataTable.Buttons(elementos.tablaPuntadas, { buttons: [ { extend: 'excelHtml5', className:'buttons-excel', title: 'Ranking de Puntadas por Máquina', messageTop: encabezadoExcel, messageBottom: pieDePagina }, { extend: 'print', className:'buttons-print', title: 'Ranking de Puntadas por Máquina', messageTop: encabezadoImprimir, messageBottom: pieDePagina, customize: function(win){$(win.document.body).css('background','none').find('h1').css('text-align','center');$(win.document.body).css('font-size','10pt').find('table').addClass('compact').css('font-size','inherit');} } ] });
            $('#dlPuntadas').on('click', () => elementos.tablaPuntadas.buttons('.buttons-excel').trigger());
            $('#prPuntadas').on('click', () => elementos.tablaPuntadas.buttons('.buttons-print').trigger());
        }
        
        const descargarGrafico = (grafico, nombreArchivo) => {
            if (grafico) {
                const link = document.createElement('a');
                link.href = grafico.toBase64Image('image/png', 1);
                link.download = `${nombreArchivo}_${fechas.fechaInicio}_a_${fechas.fechaFin}.png`;
                link.click();
            }
        };

        const imprimirGrafico = (grafico) => {
            if (grafico) {
                const dataUrl = grafico.toBase64Image();
                const ventanaImpresion = window.open('', '_blank');
                ventanaImpresion.document.write(`<html><head><title>Imprimir Gráfico</title></head><body style="font-family: Arial, sans-serif;">${encabezadoImprimir}<div style="text-align:center; margin-top: 20px;"><img src="${dataUrl}" style="max-width: 95%;" onload="window.print(); setTimeout(function(){window.close();}, 100);"></div><p style="text-align:center; font-size:10px; margin-top:15px;">${pieDePagina}</p></body></html>`);
                ventanaImpresion.document.close();
            }
        };

        $('#dlGraficoUnidades').on('click', () => descargarGrafico(elementos.graficoUnidades, 'Grafico_Unidades_por_Maquina'));
        $('#prGraficoUnidades').on('click', () => imprimirGrafico(elementos.graficoUnidades));
        $('#dlGraficoPuntadas').on('click', () => descargarGrafico(elementos.graficoPuntadas, 'Grafico_Puntadas_por_Maquina'));
        $('#prGraficoPuntadas').on('click', () => imprimirGrafico(elementos.graficoPuntadas));
    }
    // ===============================================
    // ===== FIN DE LA CORRECCIÓN =====
    // ===============================================
});