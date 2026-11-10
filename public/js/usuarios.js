// 1. Cargar la lista de roles desde la base de datos (Retorna una Promesa AJAX)
function cargarRoles(idSeleccionado = null) {
    return $.ajax({
        url: 'index.php?controller=usuario&action=obtenerRoles',
        type: 'GET',
        dataType: 'json',
        success: function (resRoles) {
            var $selectRol = $('#perfil');
            $selectRol.empty().append('<option value="">-- Seleccione Rol --</option>');

            if (resRoles.status === 'success' && Array.isArray(resRoles.data)) {
                $.each(resRoles.data, function (i, r) {
                    var selected = (idSeleccionado && idSeleccionado == r.id) ? 'selected' : '';
                    $selectRol.append('<option value="' + r.id + '" ' + selected + '>' + r.nombre + '</option>');
                });
            }
        }
    });
}

// 2. FUNCIÓN GLOBAL: Abrir modal limpio para crear usuario
function nuevoUsuario() {
    if (typeof $ === 'undefined') return;

    $('#formUsuario')[0].reset();
    $('#usuario_id').val('');
    $('#modalTitle').text('Nuevo Usuario');

    // Cargar roles dinámicos al crear
    cargarRoles().then(function () {
        $('#modalUsuario').modal('show');
    });
}

// 3. FUNCIÓN GLOBAL: Editar usuario existente
function editarUsuario(id) {
    if (typeof $ === 'undefined') return;

    $('#formUsuario')[0].reset();
    $('#usuario_id').val(id);
    $('#modalTitle').text('Editar Usuario #' + id);

    // Cargar datos del usuario por su ID
    $.ajax({
        url: 'index.php?controller=usuario&action=obtenerPorId',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success' && res.data) {
                var u = res.data;
                $('#nombre').val(u.nombre);
                $('#documento').val(u.documento || '');
                $('#mail').val(u.mail || u.correo || '');
                $('#telefono').val(u.telefono || '');
                $('#estado').val(u.estado);

                // Cargar roles dinámicos y seleccionar el actual del usuario
                cargarRoles(u.perfil || u.id_perfil).then(function () {
                    $('#modalUsuario').modal('show');
                });
            } else {
                Swal.fire('Error', 'No se pudieron consultar los datos del usuario.', 'error');
            }
        },
        error: function () {
            Swal.fire('Error', 'Fallo al consultar el servidor.', 'error');
        }
    });
}

// 4. FUNCIÓN GLOBAL: Cambiar Estado (Activar/Inactivar)
function cambiarEstado(id, estadoActual) {
    var nuevoEstado = (parseInt(estadoActual) === 1) ? 0 : 1;
    var accionTexto = (nuevoEstado === 0) ? 'inactivar' : 'activar';

    Swal.fire({
        title: '¿Confirmar acción?',
        text: `¿Deseas ${accionTexto} a este usuario?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: (nuevoEstado === 0) ? '#d33' : '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${accionTexto}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'index.php?controller=usuario&action=cambiarEstado',
                type: 'POST',
                data: { id: id, estado: nuevoEstado },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        $('#tablaUsuarios').DataTable().ajax.reload(null, false);
                        Swal.fire('¡Actualizado!', response.message, 'success');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error');
                }
            });
        }
    });
}

// 5. EVENTOS INICIALES (DataTables y Submit de Formulario)
document.addEventListener("DOMContentLoaded", function () {
    if ($.fn.DataTable.isDataTable('#tablaUsuarios')) {
        $('#tablaUsuarios').DataTable().destroy();
    }

    $('#tablaUsuarios').DataTable({
        "ajax": {
            "url": "index.php?controller=usuario&action=listar",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            { "data": "nombre" },
            { "data": "mail" },
            {
                "data": "nombre_perfil",
                "render": function (data) {
                    return `<span class="badge badge-info">${data || 'USUARIO'}</span>`;
                }
            },
            {
                "data": "estado",
                "render": function (data) {
                    return (parseInt(data) === 1) ?
                        '<span class="badge badge-success">Activo</span>' :
                        '<span class="badge badge-danger">Inactivo</span>';
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `
                        <button class="btn btn-warning btn-sm" onclick="editarUsuario(${row.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="cambiarEstado(${row.id}, ${row.estado})" title="Cambiar Estado">
                            <i class="fas fa-power-off"></i>
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

    // Envío del formulario vía AJAX (Evalúa si es Crear o Editar automáticamente)
    $('#formUsuario').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'index.php?controller=usuario&action=guardar',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#modalUsuario').modal('hide');
                    $('#tablaUsuarios').DataTable().ajax.reload(null, false);
                    Swal.fire('¡Éxito!', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Ocurrió un error al procesar la solicitud.', 'error');
            }
        });
    });
});