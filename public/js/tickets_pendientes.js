(function () {
    function initTicketsPendientes() {
        if (typeof $ === 'undefined') {
            setTimeout(initTicketsPendientes, 50);
            return;
        }

        $(document).ready(function () {
            var $tabla = $('#tablaTicketsPendientes');

            if ($tabla.length) {
                if ($.fn.DataTable.isDataTable('#tablaTicketsPendientes')) {
                    $tabla.DataTable().destroy();
                }

                $tabla.DataTable({
                    "ajax": {
                        "url": "index.php?controller=ticket&action=listarPendientes",
                        "type": "GET",
                        "dataSrc": "data"
                    },
                    "columns": [
                        { "data": "id" },
                        { "data": "categoria" },
                        { "data": "proyecto" },
                        { "data": "asunto" },
                        { "data": "prioridad" },
                        { "data": "usuario_solicitante" },
                        { "data": "fecha" },
                        { "data": "estado" },
                        { "data": "estado_soporte" },
                        { "data": "acciones" }
                    ],
                    "responsive": true,
                    "autoWidth": false,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                    }
                });
            }
        });
    }

    initTicketsPendientes();
})();