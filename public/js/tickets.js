(function () {
    function initTickets() {
        if (typeof $ === 'undefined') {
            setTimeout(initTickets, 50);
            return;
        }

        $(document).ready(function () {
            var tabla = $('#tablaTickets').DataTable({
                "ajax": {
                    "url": "index.php?controller=ticket&action=listarPorUsuario",
                    "type": "POST",
                    "dataSrc": "data"
                },
                "columns": [
                    { "data": "id" },
                    { "data": "titulo" },
                    { "data": "solicitante" },
                    { "data": "estado" },
                    { "data": "fecha" },
                    { "data": "acciones" }
                ],
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                }
            });

            $(document).off('submit', '#formNuevoTicket').on('submit', '#formNuevoTicket', function (e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: 'index.php?controller=ticket&action=crear',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Registrado!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#formNuevoTicket')[0].reset();
                            $('#modalNuevoTicket').modal('hide');
                            tabla.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de servidor',
                            text: 'Ocurrió un fallo al procesar la solicitud.'
                        });
                    }
                });
            });
        });
    }

    initTickets();
})();