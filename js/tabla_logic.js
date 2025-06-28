$(document).ready(function() {
    var table = $('#tabla-produccion').DataTable({
        dom: 'frtip',
        "language": {
            // ... (tu objeto de idioma completo aquí) ...
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    });

    // --- MANEJO PERSONALIZADO DE ELEMENTOS ---

    const nitEmpresa = '900.123.456-7';
    const nombreEmpresa = 'BORDADOS DEL SUR S.A.S.';

    const encabezadoExcel = `NIT de la empresa: ${nitEmpresa}\nNombre de la empresa: ${nombreEmpresa}\nFecha de generación: ${fechaReporte}`;
    const pieDePaginaExcel = `Reporte generado por: ${usuarioReporte}`;

    const encabezadoImprimir = `
        <div style="text-align:center; font-family: Arial, sans-serif;">
            <h3 style="margin-bottom: 5px;">${nombreEmpresa}</h3>
            <p style="font-size: 12px; margin: 0;">NIT: ${nitEmpresa}</p>
            <p style="font-size: 12px; margin: 0;">Fecha de Generación: ${fechaReporte}</p>
        </div>`;
    const pieDePaginaImprimir = `<p style="text-align:center; font-size:10px; margin-top:15px;">Reporte generado por: ${usuarioReporte}</p>`;

    new $.fn.dataTable.Buttons(table, {
        buttons: [
            // Botón de Excel (sin cambios)
            { 
                extend: 'excelHtml5', 
                text: 'Exportar a Excel', 
                className: 'btn btn-success',
                title: 'Informe Diario de Producción',
                messageTop: encabezadoExcel,
                messageBottom: pieDePaginaExcel
            },
            // Botón de Imprimir
            { 
                extend: 'print', 
                text: 'Imprimir', 
                className: 'btn btn-info',
                title: 'Informe Diario de Producción',
                messageTop: encabezadoImprimir,
                messageBottom: pieDePaginaImprimir,
                customize: function ( win ) {
                    var body = $(win.document.body);
                    // 1. Quita el color de fondo para una impresión limpia
                    body.css('background', 'none');

                    // 2. Centra el título principal ("Registros de Producción")
                    //    DataTables lo inserta en una etiqueta <h1>
                    body.find('h1').css('text-align', 'center');
                    body.css('font-size', '10pt');
                    body.find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                }
            }
        ]
    });
    
    table.buttons().container().appendTo('#contenedor-botones-export');
    $('.dataTables_filter').appendTo('#contenedor-buscador');
});