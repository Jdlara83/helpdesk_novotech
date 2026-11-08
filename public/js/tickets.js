(function () {
    function initTickets() {
        if (typeof $ === 'undefined') {
            setTimeout(initTickets, 50);
            return;
        }

        $(document).ready(function () {
            var $tabla = $('#tablaTickets');

            if ($tabla.length) {
                if ($.fn.DataTable.isDataTable('#tablaTickets')) {
                    $tabla.DataTable().destroy();
                }

                var tabla = $tabla.DataTable({
                    "ajax": {
                        "url": "index.php?controller=ticket&action=listarPorUsuario",
                        "type": "GET",
                        "dataSrc": "data"
                    },
                    "columns": [
                        { "data": "id" },
                        { "data": "categoria" },
                        { "data": "proyecto" },
                        { "data": "asunto" },
                        { "data": "prioridad" },
                        { "data": "fecha" },
                        { "data": "estado_soporte" },
                        { "data": "estado_ticket" },
                        { "data": "acciones" }
                    ],
                    "responsive": true,
                    "autoWidth": false,
                    "language": {
                        "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                    }
                });

                // 1. Crear Ticket Nuevo
                $(document).off('submit', '#formNuevoTicket').on('submit', '#formNuevoTicket', function (e) {
                    e.preventDefault();
                    var formData = new FormData(this);

                    $.ajax({
                        url: 'index.php?controller=ticket&action=crear',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({ icon: 'success', title: '¡Registrado!', text: response.message, timer: 1500, showConfirmButton: false });
                                $('#formNuevoTicket')[0].reset();
                                $('#modalNuevoTicket').modal('hide');
                                tabla.ajax.reload(null, false);
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                            }
                        }
                    });
                });

                // 2. Guardar Seguimiento (Muestra mensaje y cierra el modal al instante)
                $(document).off('submit', '#formNuevoSeguimiento').on('submit', '#formNuevoSeguimiento', function (e) {
                    e.preventDefault();

                    var formData = new FormData(this);

                    $.ajax({
                        url: 'index.php?controller=ticket&action=guardarSeguimiento',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                $('#modalSeguimientoTicket').modal('hide');
                                $('#formNuevoSeguimiento')[0].reset();

                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Seguimiento Guardado!',
                                    text: 'El historial del ticket ha sido actualizado correctamente.',
                                    timer: 1600,
                                    showConfirmButton: false
                                });

                                tabla.ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'No se pudo guardar el seguimiento.', 'error');
                        }
                    });
                });
            }
        });
    }

    initTickets();
})();

// Ver Detalle (Ojo Verde - Solo Lectura)
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
                $('#ver_ubicacion').text(d.detalle_ubicacion || 'N/A');
                $('#ver_fecha').text(d.fecha_crea);
                $('#ver_asunto').text(d.asunto_ticket);
                $('#ver_detalle').text(d.detalle_ticket);

                var $contenedorArchivos = $('#ver_archivos_cargados');
                $contenedorArchivos.empty();
                var tieneArchivos = false;

                if (d.ruta_imagenes && d.ruta_imagenes.trim() !== '') {
                    $.each(d.ruta_imagenes.split(','), function (i, img) {
                        if (img.trim() !== '') {
                            tieneArchivos = true;
                            $contenedorArchivos.append('<a href="' + img + '" target="_blank" class="btn btn-outline-info btn-sm mr-2 mb-2"><i class="fas fa-image"></i> Ver Imagen ' + (i + 1) + '</a>');
                        }
                    });
                }

                if (d.ruta_archivos && d.ruta_archivos.trim() !== '') {
                    $.each(d.ruta_archivos.split(','), function (i, arch) {
                        if (arch.trim() !== '') {
                            tieneArchivos = true;
                            $contenedorArchivos.append('<a href="' + arch + '" target="_blank" class="btn btn-outline-secondary btn-sm mr-2 mb-2"><i class="fas fa-paperclip"></i> Ver Documento ' + (i + 1) + '</a>');
                        }
                    });
                }

                if (!tieneArchivos) {
                    $contenedorArchivos.html('<span class="text-muted italic">No hay archivos ni imágenes adjuntas.</span>');
                }

                $('#modalVerDetalleTicket').modal('show');
            }
        }
    });
}

// Abrir Modal de Seguimiento (Globo Azul)
function abrirSeguimiento(idTicket) {
    if (typeof $ === 'undefined') return;

    $('#seg_id_ticket').text('#' + idTicket);
    $('#seg_input_id_ticket').val(idTicket);
    $('#formNuevoSeguimiento')[0].reset();

    $.ajax({
        url: 'index.php?controller=ticket&action=obtenerDetalle',
        type: 'GET',
        data: { id_ticket: idTicket },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success' && response.data) {
                $('#seg_asunto').text(response.data.asunto_ticket);
                $('#seg_categoria').text(response.data.categoria);
                cargarHistorialSeguimiento(idTicket);
                $('#modalSeguimientoTicket').modal('show');
            }
        }
    });
}

// Cargar conversación en el modal de seguimiento
function cargarHistorialSeguimiento(idTicket) {
    $.ajax({
        url: 'index.php?controller=ticket&action=obtenerSeguimientos',
        type: 'GET',
        data: { id_ticket: idTicket },
        dataType: 'json',
        success: function (response) {
            var $historial = $('#ver_historial_seguimiento');
            $historial.empty();

            if (response.status === 'success' && Array.isArray(response.data) && response.data.length > 0) {
                $.each(response.data, function (i, seg) {
                    var adjuntoHTML = '';
                    if (seg.archivo_mensaje) {
                        adjuntoHTML = '<br><a href="' + seg.archivo_mensaje + '" target="_blank" class="btn btn-sm btn-outline-primary mt-1"><i class="fas fa-download"></i> Ver Adjunto</a>';
                    }

                    $historial.append(
                        '<div class="p-2 mb-2 bg-white rounded border shadow-sm">' +
                        '<div class="d-flex justify-content-between text-muted small mb-1">' +
                        '<strong><i class="fas fa-user"></i> ' + (seg.usuario || 'Usuario') + '</strong>' +
                        '<span><i class="fas fa-clock"></i> ' + seg.fecha + '</span>' +
                        '</div>' +
                        '<p class="mb-0 text-dark">' + seg.mensaje + '</p>' +
                        adjuntoHTML +
                        '</div>'
                    );
                });
                $historial.scrollTop($historial[0].scrollHeight);
            } else {
                $historial.html('<span class="text-muted italic">No hay historial de seguimiento registrado aún.</span>');
            }
        }
    });
}