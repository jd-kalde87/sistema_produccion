$(document).ready(function() {
    // Escuchar el evento de envío del formulario
    $('#filtro-colaborador-form').on('submit', function(e) {
        // Prevenir la recarga de la página
        e.preventDefault();

        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();
        const resultadoDiv = $('#resultado-informe');

        // Validación simple
        if (!fechaInicio || !fechaFin) {
            resultadoDiv.html('<p class="alert alert-danger">Ambas fechas son obligatorias.</p>');
            return;
        }

        // Mostrar un mensaje de carga
        resultadoDiv.html('<p class="alert alert-info">Buscando...</p>');

        // Hacer la llamada a la API usando AJAX de jQuery
        $.ajax({
            url: 'api_top_colaborador.php',
            type: 'GET',
            data: {
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            },
            dataType: 'json',
            success: function(response) {
                let html = '';
                if (response.status === 'success') {
                    // Si se encontró un resultado, lo mostramos
                    const data = response.data;
                    html = `
                        <div class="alert alert-success">
                            <p><strong>Colaborador Destacado:</strong> ${data.colaborador.toUpperCase()}</p>
                            <p><strong>Total de Unidades Producidas:</strong> ${new Intl.NumberFormat().format(data.total_unidades)}</p>
                        </div>
                    `;
                } else {
                    // Si no hay datos o hay un error, mostramos el mensaje de la API
                    html = `<p class="alert alert-danger">${response.message}</p>`;
                }
                resultadoDiv.html(html);
            },
            error: function() {
                // En caso de un error de conexión con la API
                resultadoDiv.html('<p class="alert alert-danger">Error: No se pudo conectar con el servidor para generar el informe.</p>');
            }
        });
    });
});