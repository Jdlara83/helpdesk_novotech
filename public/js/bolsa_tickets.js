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

            // Enviar formulario del Modal "Asignar a Tercero"
            $('#formAsignarTicket').off('submit').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: 'index.php?controller=ticket&action=asignarTercero',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#modalAsignarTicket').modal('hide');
                            $('#tablaBolsaTickets').DataTable().ajax.reload(null, false);
                            Swal.fire('¡Éxito!', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'No se pudo procesar la asignación.', 'error');
                    }
                });
            });
        });
    }

    initBolsaTickets();
})();

// ==========================================
// FUNCIONES GLOBALES (Botones de la Tabla)
// ==========================================

// 1. Botón Azul (Check): Abrir Modal para Asignar a un Usuario de Soporte
function abrirModalAsignar(idTicket) {
    if (typeof $ === 'undefined') return;

    $('#asignar_id_ticket').val(idTicket);

    $.ajax({
        url: 'index.php?controller=ticket&action=obtenerSoportes',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success' && Array.isArray(response.data)) {
                var $select = $('#select_usuario_asignar');
                $select.empty().append('<option value="">-- Seleccione un Agente --</option>');

                $.each(response.data, function (i, user) {
                    $select.append('<option value="' + user.id + '">' + user.nombre + '</option>');
                });

                $('#modalAsignarTicket').modal('show');
            } else {
                Swal.fire('Atención', 'No hay agentes de soporte disponibles.', 'warning');
            }
        },
        error: function () {
            Swal.fire('Error', 'No se pudo cargar la lista de soporte.', 'error');
        }
    });
}


// 3. Botón Verde (Usuario): Auto-asignarme el Ticket (Tomar Caso)
function tomarTicket(idTicket) {
    if (typeof $ === 'undefined') return;

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
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo tomar el ticket.', 'error');
                }
            });
        }
    });

}
// 2. Botón Amarillo (Ojo): Ver Detalle en Modal (Solo Lectura)
function verDetalleTicket(idTicket) {
    if (typeof $ === 'undefined') return;

    $.ajax({
        url: 'index.php?controller=ticket&action=obtenerDetalle',
        type: 'GET',
        data: { id_ticket: idTicket },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success' && response.data) {
                var d = response.data;

                $('#ver_id_ticket').text('#' + d.id_ticket);
                $('#ver_categoria').text(d.categoria);
                $('#ver_proyecto').text(d.proyecto);
                $('#ver_prioridad').text(d.prioridad);
                $('#ver_estado').text(d.estado);
                $('#ver_solicitante').text(d.solicitante);
                $('#ver_ubicacion').text(d.detalle_ubicacion || 'Sin ubicación registrada');
                $('#ver_fecha').text(d.fecha_crea);
                $('#ver_asunto').text(d.asunto_ticket);
                $('#ver_detalle').text(d.detalle_ticket);

                // Renderizar evidencias y archivos adjuntos
                var $contenedorArchivos = $('#ver_archivos_cargados');
                $contenedorArchivos.empty();

                var tieneArchivos = false;

                if (d.ruta_imagenes && d.ruta_imagenes.trim() !== '') {
                    var imgs = d.ruta_imagenes.split(',');
                    $.each(imgs, function (i, img) {
                        if (img.trim() !== '') {
                            tieneArchivos = true;
                            $contenedorArchivos.append(
                                '<a href="' + img + '" target="_blank" class="btn btn-outline-info btn-sm mr-2 mb-2">' +
                                '<i class="fas fa-image"></i> Ver Imagen ' + (i + 1) + '</a>'
                            );
                        }
                    });
                }

                if (d.ruta_archivos && d.ruta_archivos.trim() !== '') {
                    var archs = d.ruta_archivos.split(',');
                    $.each(archs, function (i, arch) {
                        if (arch.trim() !== '') {
                            tieneArchivos = true;
                            $contenedorArchivos.append(
                                '<a href="' + arch + '" target="_blank" class="btn btn-outline-secondary btn-sm mr-2 mb-2">' +
                                '<i class="fas fa-paperclip"></i> Ver Documento ' + (i + 1) + '</a>'
                            );
                        }
                    });
                }

                if (!tieneArchivos) {
                    $contenedorArchivos.html('<span class="text-muted italic">No hay archivos ni imágenes adjuntas.</span>');
                }

                $('#modalVerDetalleTicket').modal('show');
            } else {
                Swal.fire('Error', 'No se pudo cargar la información del ticket.', 'error');
            }
        },
        error: function () {
            Swal.fire('Error', 'Ocurrió un fallo al consultar el detalle.', 'error');
        }
    });
}