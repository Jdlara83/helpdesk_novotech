(function () {
    function initRequerimientos() {
        if (typeof $ === 'undefined') {
            setTimeout(initRequerimientos, 50);
            return;
        }

        $(document).ready(function () {
            var $tabla = $('#tablaRequerimientos');

            if ($tabla.length) {
                if ($.fn.DataTable.isDataTable('#tablaRequerimientos')) {
                    $tabla.DataTable().destroy();
                }

                $tabla.DataTable({
                    "ajax": {
                        "url": "index.php?controller=requerimiento&action=listarPorUsuario",
                        "type": "GET",
                        "dataSrc": "data"
                    },
                    "columns": [
                        { "data": "id" },
                        { "data": "asunto" },
                        { "data": "solicitante" },
                        { "data": "estado" },
                        { "data": "fecha" },
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

    initRequerimientos();
})();