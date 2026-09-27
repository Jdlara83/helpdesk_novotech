(function () {
    function initBolsaTickets() {
        if (typeof $ === 'undefined') {
            setTimeout(initBolsaTickets, 50);
            return;
        }

        $(document).ready(function () {
            var $tabla = $('#tablaBolsaTickets');

            if ($tabla.length) {
                if ($.fn.DataTable.isDataTable('#tablaBolsaTickets')) {
                    $tabla.DataTable().destroy();
                }

                $tabla.DataTable({
                    "ajax": {
                        "url": "index.php?controller=ticket&action=listarBolsa",
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

    initBolsaTickets();
})();

// Función global para tomar un ticket
function tomarTicket(idTicket) {
    Swal.fire({
        title: '¿Tomar este ticket?',
        text: 'Te asignarás como responsable del soporte de esta solicitud.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, tomar caso',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'index.php?controller=ticket&action=tomarTicket',
                type: 'POST',
                data: { id_ticket: idTicket },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        $('#tablaBolsaTickets').DataTable().ajax.reload(null, false);
                        Swal.fire('¡Asignado!', response.message, 'success');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
}