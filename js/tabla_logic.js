$(document).ready(function() {
    // Inicializamos la tabla, pero ahora SÍ incluimos la 'f' en la opción 'dom'.
    // Esto es para que DataTables cree el elemento del buscador.
    var table = $('#tabla-produccion').DataTable({
        dom: 'frtip', // <-- ¡LA 'f' HA VUELTO! ESTE ES EL CAMBIO CLAVE.
        "language": {
            // (Tu configuración de idioma no cambia)
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });

    // --- MANEJO PERSONALIZADO DE ELEMENTOS ---

    // 1. Mover los botones de exportación a su contenedor
    new $.fn.dataTable.Buttons(table, {
        buttons: [
            { extend: 'excelHtml5', text: 'Exportar a Excel', className: 'btn' },
            { extend: 'print', text: 'Imprimir', className: 'btn' }
        ]
    });
    table.buttons().container().appendTo('#contenedor-botones-export');

    // 2. Mover el campo de búsqueda a su contenedor
    // Esta línea ahora funcionará porque el elemento .dataTables_filter SÍ existe.
    $('.dataTables_filter').appendTo('#contenedor-buscador');
});