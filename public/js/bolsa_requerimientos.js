(function () {
    function initBolsaRequerimientos() {
        if (typeof $ === 'undefined') {
            setTimeout(initBolsaRequerimientos, 50);
            return;
        }

        $(document).ready(function () {
            var $tabla = $('#tablaBolsaRequerimientos');

            if ($tabla.length) {
                if ($.fn.DataTable.isDataTable('#tablaBolsaRequerimientos')) {
                    $tabla.DataTable().destroy();
                }

                $tabla.DataTable({
                    "ajax": {
                        "url": "index.php?controller=requerimiento&action=listarBolsa",
                        "type": "GET",
                        "dataSrc": "data"
                    },
                    "columns": [
                        { "data": "id" },
                        { "data": "categoria" },
                        { "data": "proyecto" },
                        { "data": "asunto" },
                        /*{ "data": "prioridad" },*/
                        { "data": "usuario_solicitante" },
                        { "data": "fecha" },
                        { "data": "estado" },
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

    initBolsaRequerimientos();
})();