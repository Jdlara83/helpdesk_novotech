// Función global para abrir el Modal y poblar selects vía AJAX
function nuevoResponsable() {
    $('#formResponsable')[0].reset();

    // 1. Cargar Categorías
    $.ajax({
        url: 'index.php?controller=responsable&action=listarCategorias',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            var $selectCat = $('#id_categoria_soporte');
            $selectCat.empty().append('<option value="">-- Seleccione una opción --</option>');
            if (response.data && response.data.length > 0) {
                $.each(response.data, function (i, item) {
                    $selectCat.append(`<option value="${item.id}">${item.categoria}</option>`);
                });
            }
        }
    });

    // 2. Cargar Proyectos
    $.ajax({
        url: 'index.php?controller=responsable&action=listarProyectos',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            var $selectProy = $('#id_proyecto_soporte');
            $selectProy.empty().append('<option value="">-- Seleccione un proyecto --</option>');
            if (response.data && response.data.length > 0) {
                $.each(response.data, function (i, item) {
                    $selectProy.append(`<option value="${item.id}">${item.proyecto}</option>`);
                });
            }
        }
    });

    // 3. Cargar Usuarios
    $.ajax({
        url: 'index.php?controller=usuario&action=listar',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            var $selectUsu = $('#id_usuario');
            $selectUsu.empty().append('<option value="">-- Seleccione un usuario --</option>');
            if (response.data && response.data.length > 0) {
                $.each(response.data, function (i, item) {
                    $selectUsu.append(`<option value="${item.id}">${item.nombre} - ${item.mail || item.usuario}</option>`);
                });
            }
        }
    });

    $('#modalResponsable').modal('show');
}

// Función global para eliminar asignaciones
function eliminarResponsable(id) {
    Swal.fire({
        title: '¿Confirmar eliminación?',
        text: 'Se removerá la asignación de este responsable.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'index.php?controller=responsable&action=eliminar',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        $('#tablaResponsables').DataTable().ajax.reload(null, false);
                        Swal.fire('¡Eliminado!', response.message, 'success');
                    } else if (response.status === 'session_expired') {
                        window.location.href = 'index.php?controller=auth&action=login';
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
}

// Inicializador seguro: Espera a que el evento global cargue jQuery sin fallar en la consola
function iniciarModuloResponsables() {
    if (typeof jQuery === 'undefined') {
        setTimeout(iniciarModuloResponsables, 50);
        return;
    }

    $(document).ready(function () {
        var $tabla = $('#tablaResponsables');

        if ($tabla.length) {
            if ($.fn.DataTable.isDataTable('#tablaResponsables')) {
                $tabla.DataTable().destroy();
            }

            $tabla.DataTable({
                "ajax": {
                    "url": "index.php?controller=responsable&action=listar",
                    "type": "GET",
                    "dataSrc": function (json) {
                        if (json.status === 'session_expired') {
                            window.location.href = 'index.php?controller=auth&action=login';
                            return [];
                        }
                        return json.data || [];
                    }
                },
                "columns": [
                    { "data": "id" },
                    {
                        "data": "categoria",
                        "render": function (data) {
                            return `<span class="badge badge-info p-2">${data}</span>`;
                        }
                    },
                    {
                        "data": "proyecto",
                        "render": function (data) {
                            return `<strong>${data}</strong>`;
                        }
                    },
                    {
                        "data": null,
                        "render": function (data, type, row) {
                            return `<strong>${row.nombre_usuario}</strong> (${row.mail || row.usuario || ''})`;
                        }
                    },
                    {
                        "data": null,
                        "render": function (data, type, row) {
                            return `
                                <button class="btn btn-danger btn-sm" onclick="eliminarResponsable(${row.id})" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            `;
                        }
                    }
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "responsive": true,
                "autoWidth": false
            });
        }

        // Interceptar submit AJAX de forma segura
        $('#formResponsable').off('submit').on('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var formData = $(this).serialize();

            $.ajax({
                url: 'index.php?controller=responsable&action=guardar',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        $('#modalResponsable').modal('hide');
                        $('#tablaResponsables').DataTable().ajax.reload(null, false);
                        Swal.fire('¡Éxito!', response.message, 'success');
                    } else if (response.status === 'session_expired') {
                        window.location.href = 'index.php?controller=auth&action=login';
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo procesar la asignación.', 'error');
                }
            });

            return false;
        });
    });
}

// Ejecutar el cargador seguro
iniciarModuloResponsables();