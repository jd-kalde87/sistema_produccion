$(document).ready(function() {
    let puntadasChartInstance = null;
    let unidadesChartInstance = null;

    $('#filtro-maquinaria-form').on('submit', function(e) {
        e.preventDefault();

        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();
        const resultadoDiv = $('#resultado-informe');

        if (!fechaInicio || !fechaFin) {
            resultadoDiv.html('<p class="alert alert-danger">Ambas fechas son obligatorias.</p>');
            return;
        }

        resultadoDiv.html('<p class="alert alert-info">Cargando datos de los informes...</p>');

        $.ajax({
            url: 'api_informe_maquinaria.php',
            type: 'GET',
            data: {
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            },
            dataType: 'json',
            success: function(response) {
                resultadoDiv.empty();
                renderPuntadasChart(response.puntadasData);
                renderUnidadesChart(response.unidadesData);
            },
            error: function() {
                resultadoDiv.html('<p class="alert alert-danger">Error al conectar con el servidor.</p>');
            }
        });
    });

    /**
     * Función para dibujar el gráfico de Total de Puntadas por Máquina
     */
    function renderPuntadasChart(data) {
        const resultadoDiv = $('#resultado-informe');
        
        if (!data || data.length === 0) {
            resultadoDiv.append('<h3>Total de Puntadas por Máquina</h3><p class="alert alert-info">No hay datos de puntadas para mostrar en este rango.</p>');
            return;
        }

        // --- INICIO DE LA MODIFICACIÓN ---
        const labels = data.map(item => item.maquina_etiqueta); // Usamos el nuevo campo de la API
        // --- FIN DE LA MODIFICACIÓN ---
        const values = data.map(item => item.total_puntadas);

        const chartContainerHtml = `
            <div class="seccion-consulta">
                <h3>Total de Puntadas por Máquina</h3>
                <div class="tabla-contenedor" style="padding: 15px; height: 400px; position: relative;">
                    <canvas id="puntadasChart"></canvas>
                </div>
            </div>
        `;
        resultadoDiv.append(chartContainerHtml);

        if (puntadasChartInstance) {
            puntadasChartInstance.destroy();
        }

        const ctx = document.getElementById('puntadasChart').getContext('2d');
        puntadasChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total de Puntadas',
                    data: values,
                    backgroundColor: 'rgba(0, 123, 255, 0.6)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    }

    /**
     * Función para dibujar el gráfico de Cantidad de Unidades por Máquina
     */
    function renderUnidadesChart(data) {
        const resultadoDiv = $('#resultado-informe');

        if (!data || data.length === 0) {
            resultadoDiv.append('<h3>Unidades Producidas por Máquina</h3><p class="alert alert-info">No hay datos de unidades para mostrar en este rango.</p>');
            return;
        }

        // --- INICIO DE LA MODIFICACIÓN ---
        const labels = data.map(item => item.maquina_etiqueta); // Usamos el nuevo campo de la API
        // --- FIN DE LA MODIFICACIÓN ---
        const values = data.map(item => item.total_unidades);
        
        const chartContainerHtml = `
            <div class="seccion-consulta">
                <h3>Unidades Producidas por Máquina</h3>
                <div class="tabla-contenedor" style="padding: 15px; height: 400px; position: relative;">
                    <canvas id="unidadesChart"></canvas>
                </div>
            </div>
        `;
        resultadoDiv.append(chartContainerHtml);

        if (unidadesChartInstance) {
            unidadesChartInstance.destroy();
        }

        const ctx = document.getElementById('unidadesChart').getContext('2d');
        unidadesChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad de Unidades',
                    data: values,
                    backgroundColor: 'rgba(40, 167, 69, 0.6)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }
});