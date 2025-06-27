$(document).ready(function() {
    let lineChartInstance = null;

    $('#filtro-lineas-form').on('submit', function(e) {
        e.preventDefault();

        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();
        // --- INICIO DE LA MODIFICACIÓN ---
        const maquinaId = $('#maquina').val(); // Obtenemos el valor del nuevo select
        // --- FIN DE LA MODIFICACIÓN ---
        const resultadoDiv = $('#resultado-informe');

        if (!fechaInicio || !fechaFin) {
            // El resto del código de validación y AJAX no cambia mucho
            return;
        }
        
        if (lineChartInstance) {
            lineChartInstance.destroy();
        }
        resultadoDiv.html('<p class="alert alert-info">Generando gráfico de tendencias...</p>');

        $.ajax({
            url: 'api_produccion_diaria_maquina.php',
            type: 'GET',
            data: {
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin,
                // --- INICIO DE LA MODIFICACIÓN ---
                id_maquina: maquinaId // Enviamos el ID de la máquina a la API
                // --- FIN DE LA MODIFICACIÓN ---
            },
            dataType: 'json',
            success: function(response) {
                resultadoDiv.empty(); 
                
                if (!response.labels || response.labels.length === 0) {
                    resultadoDiv.html('<p class="alert alert-info">No se encontraron datos con los filtros seleccionados.</p>');
                    return;
                }
                
                const chartContainerHtml = `
                    <div class="tabla-contenedor" style="padding: 15px; height: 500px; position: relative;">
                        <canvas id="puntadasLineaChart"></canvas>
                    </div>`;
                resultadoDiv.html(chartContainerHtml);

                renderizarGraficoDeLineas(response);
            },
            error: function() {
                resultadoDiv.html('<p class="alert alert-danger">Error: No se pudo conectar con el servidor.</p>');
            }
        });
    });

    function renderizarGraficoDeLineas(chartData) {
        const aColores = [
            '#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', 
            '#6f42c1', '#f8f9fa', '#343a40'
        ];

        chartData.datasets.forEach((dataset, index) => {
            dataset.borderColor = aColores[index % aColores.length];
            dataset.backgroundColor = aColores[index % aColores.length] + '33';
            dataset.fill = false;
            dataset.tension = 0.1;
        });

        const ctx = document.getElementById('puntadasLineaChart').getContext('2d');
        lineChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Puntadas por Máquina a lo Largo del Tiempo' }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Total de Puntadas' } }
                }
            }
        });
    }
});