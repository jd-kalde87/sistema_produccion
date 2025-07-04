$(document).ready(function() {
    Chart.register(ChartDataLabels);
    let chartInstances = {};

    $('#filtro-dashboard-form').on('submit', function(e) {
        e.preventDefault();
        const fechaInicio = $('#fecha_inicio').val(), fechaFin = $('#fecha_fin').val();
        const resultadoDiv = $('#resultado-informe');
        if (!fechaInicio || !fechaFin) { return; }

        resultadoDiv.html('<p class="alert alert-info">Generando dashboard...</p>');
        Object.values(chartInstances).forEach(chart => { if(chart) chart.destroy(); });
        chartInstances = {};

        $.ajax({
            url: 'api_dashboard_general.php',
            type: 'GET',
            data: { fecha_inicio: fechaInicio, fecha_fin: fechaFin },
            dataType: 'json',
            success: function(response) {
                resultadoDiv.empty();
                resultadoDiv.html(`
                    <div class="seccion-consulta" id="chart-turno-container" style="margin-bottom: 20px;"></div>
                    <div class="seccion-consulta" id="chart-bordado-container" style="margin-bottom: 20px;"></div>
                    <div class="seccion-consulta" id="chart-color-container" style="margin-bottom: 20px;"></div>
                    <div class="seccion-consulta" id="chart-tamano-container"></div>
                `);
                chartInstances.turno = renderPieChart(response.turnoData, 'chart-turno-container', 'chartTurno', 'Producción por Turno', 'tipo_jornada');
                chartInstances.bordado = renderPieChart(response.bordadoData, 'chart-bordado-container', 'chartBordado', 'Producción por Tipo de Bordado', 'tipo_bordado');
                chartInstances.color = renderPieChart(response.colorData, 'chart-color-container', 'chartColor', 'Producción por Color', 'codigo_color');
                chartInstances.tamano = renderPieChart(response.tamanoData, 'chart-tamano-container', 'chartTamano', 'Producción por Tamaño', 'tamaño_pieza');
            },
            error: function() { resultadoDiv.html('<p class="alert alert-danger">Error de conexión.</p>'); }
        });
    });

    function renderPieChart(data, containerId, canvasId, title, labelField) {
        const container = $(`#${containerId}`);
        if (!data || data.length === 0) {
            container.html(`<h3>${title}</h3><p class="alert alert-info">No hay datos.</p>`);
            return null;
        }

        container.html(`
            <h3>${title}</h3>
            <div style="display: flex; align-items: center; height: 400px; margin-bottom: 10px;">
                <div style="position: relative; width: 60%; height: 100%;"><canvas id="${canvasId}"></canvas></div>
                <div id="leyenda-${canvasId}" style="width: 40%; padding-left: 20px; max-height: 400px; overflow-y: auto;"></div>
            </div>
            <div style="margin-top: 25px; text-align: center;">
                <div id="botones-${canvasId}" style="display: inline-flex; justify-content: center; gap: 10px;">
                    <button class="btn btn-success btn-sm" action="download"><i class="fas fa-download"></i> Descargar Gráfico (PNG)</button>
                    <button class="btn btn-info btn-sm" action="print"><i class="fas fa-print"></i> Imprimir Reporte</button>
                </div>
            </div>
        `);

        const labels = data.map(item => item[labelField] ? item[labelField].toUpperCase() : 'N/A');
        const values = data.map(item => item.total);
        const ctx = document.getElementById(canvasId).getContext('2d');
        const chartColors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997', '#6610f2', '#e83e8c'];
        
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: { labels: labels, datasets: [{ data: values, backgroundColor: chartColors }] },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: { display: false },
                    title: { display: true, text: title, font: { size: 16 } }
                }
            }
        });

        generarLeyendaPersonalizada(chart, `leyenda-${canvasId}`);
        
        $(`#botones-${canvasId} button`).on('click', function() {
            const action = $(this).attr('action');
            if (action === 'download') {
                descargarGrafico(chart, title);
            } else if (action === 'print') {
                // Ahora pasamos también el ID del contenedor de la leyenda
                imprimirGrafico(chart, title, `leyenda-${canvasId}`);
            }
        });
        return chart;
    }

    function generarLeyendaPersonalizada(chart, containerId) {
        const leyendaContainer = document.getElementById(containerId);
        const data = chart.data;
        let html = '<ul style="list-style: none; padding: 0; margin: 0; font-size: 14px;">';
        const total = data.datasets[0].data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
        data.labels.forEach((label, i) => {
            const color = data.datasets[0].backgroundColor[i];
            const value = data.datasets[0].data[i];
            const percentage = total > 0 ? ((value / total) * 100).toFixed(2) + '%' : '0.00%';
            html += `<li style="display: flex; align-items: center; margin-bottom: 8px;"><span style="height: 15px; width: 15px; background-color: ${color}; border-radius: 3px; display: inline-block; margin-right: 8px;"></span><span>${label}: <strong>${new Intl.NumberFormat().format(value)}</strong> (${percentage})</span></li>`;
        });
        html += '</ul>';
        leyendaContainer.innerHTML = html;
    }
    
    // --- LÓGICA DE EXPORTACIÓN (MODIFICADA) ---
    const nitEmpresa = '900.123.456-7', nombreEmpresa = 'BORDADOS DEL SUR S.A.S.';
    const pieDePagina = `<p style="text-align:center;font-size:10px;font-family:Arial,sans-serif;margin-top:15px;">Generado por: ${usuarioReporte}</p>`;
    
    function descargarGrafico(grafico, titulo) {
        if(grafico){
            const link = document.createElement('a');
            link.href = grafico.toBase64Image('image/png', 1);
            link.download = `${titulo.replace(/ /g, '_')}_${new Date().toISOString().slice(0,10)}.png`;
            link.click();
        }
    }

    // ===============================================
    // ===== INICIO DE LA MODIFICACIÓN =====
    // ===============================================
    function imprimirGrafico(grafico, titulo, leyendaId) {
        if(grafico){
            const dataUrl = grafico.toBase64Image();
            const leyendaHtml = document.getElementById(leyendaId).innerHTML; // Obtenemos el HTML de la leyenda
            const ventanaImpresion = window.open('', '_blank');
            const encabezadoImprimir = `<div style="text-align:center;font-family:Arial,sans-serif"><h3>${nombreEmpresa}</h3><p>NIT: ${nitEmpresa}<br>Fecha: ${fechaReporte}</p></div>`;
            
            // Construimos el nuevo cuerpo del documento de impresión
            ventanaImpresion.document.write(`
                <html>
                    <head><title>Imprimir: ${titulo}</title></head>
                    <body style="font-family:Arial,sans-serif;">
                        ${encabezadoImprimir}
                        <h2 style="text-align:center;font-size:16px;margin-top:20px;">${titulo}</h2>
                        
                        <table style="width:100%; margin-top:15px; border-collapse: collapse;">
                            <tr>
                                <td style="width:60%; vertical-align: middle; text-align: center;">
                                    <img src="${dataUrl}" style="max-width:95%;">
                                </td>
                                <td style="width:40%; vertical-align: middle; padding-left: 20px;">
                                    ${leyendaHtml}
                                </td>
                            </tr>
                        </table>
                        
                        ${pieDePagina}
                    </body>
                </html>`);
            
            // Esperamos a que la imagen cargue antes de imprimir
            ventanaImpresion.document.addEventListener('DOMContentLoaded', function() {
                const img = ventanaImpresion.document.querySelector('img');
                if (img.complete) {
                    ventanaImpresion.print();
                    setTimeout(() => ventanaImpresion.close(), 100);
                } else {
                    img.onload = () => {
                        ventanaImpresion.print();
                        setTimeout(() => ventanaImpresion.close(), 100);
                    };
                }
            });
            ventanaImpresion.document.close();
        }
    }
    // ===============================================
    // ===== FIN DE LA MODIFICACIÓN =====
    // ===============================================
});