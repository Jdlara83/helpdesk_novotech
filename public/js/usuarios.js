
document.addEventListener("DOMContentLoaded", function () {
    $('#tablaUsuarios').DataTable({
        "ajax": {
            "url": "index.php?controller=usuario&action=listar",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [{
            "data": "id"
        },
        {
            "data": "nombre"
        },
        {
            "data": "mail"
        },
        {
            "data": "nombre_perfil",
            "render": function (data) {
                return `<span class="badge badge-info">${data || 'SIN PERFIL'}</span>`;
            }
        },
        {
            "data": "estado",
            "render": function (data) {
                if (parseInt(data) === 1) {
                    return '<span class="badge badge-success">Activo</span>';
                } else {
                    return '<span class="badge badge-danger">Inactivo</span>';
                }
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
});
// Abrir el Modal Limpio para Crear Usuario
function nuevoUsuario() {
    $('#formUsuario')[0].reset();
    $('#usuario_id').val('');
    $('#modalTitle').text('Nuevo Usuario');
    $('#modalUsuario').modal('show');
}

// Capturar el envío del formulario vía AJAX
document.addEventListener("DOMContentLoaded", function () {
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

                    // Alerta interactiva con SweetAlert2
                    Swal.fire('¡Éxito!', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error',
                    'Ocurrió un error al procesar la solicitud en el servidor.', 'error'
                );
            }
        });
    });
    // 1. Declarar la función en el scope global para que DataTables la reconozca
    // function editarUsuario(id) {
    //     $.ajax({
    //         url: 'index.php?controller=usuario&action=obtener&id=' + id,
    //         type: 'GET',
    //         dataType: 'json',
    //         success: function(response) {
    //             if (response.status === 'success') {
    //                 var u = response.data;
    //                 $('#usuario_id').val(u.id);
    //                 $('#nombre').val(u.nombre);
    //                 $('#documento').val(u.documento);
    //                 $('#mail').val(u.mail);
    //                 $('#telefono').val(u.telefono);
    //                 $('#perfil').val(u.perfil);
    //                 $('#estado').val(u.estado);
    //                 $('#password').val(''); // Vacío por seguridad

    //                 $('#modalTitle').text('Editar Usuario');
    //                 $('#modalUsuario').modal('show');
    //             } else {
    //                 Swal.fire('Error', response.message, 'error');
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             console.error("Error AJAX:", xhr.responseText);
    //             Swal.fire('Error', 'No se pudieron obtener los datos del usuario.', 'error');
    //         }
    //     });
    // }

    // // 2. Inicialización de DataTables y envío de formulario
    // document.addEventListener("DOMContentLoaded", function() {
    //     // Código de DataTables y submit del formulario que ya tienes funcionando...
    // });
});

// 1. FUNCIÓN GLOBAL: Debe estar por fuera para que el onclick del HTML la encuentre
function editarUsuario(id) {
    $.ajax({
        url: 'index.php?controller=usuario&action=obtener&id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                var u = response.data;
                $('#usuario_id').val(u.id);
                $('#nombre').val(u.nombre);
                $('#documento').val(u.documento);
                $('#mail').val(u.mail);
                $('#telefono').val(u.telefono);
                $('#perfil').val(u.perfil);
                $('#estado').val(u.estado);
                $('#password').val(''); // Vacío por seguridad

                $('#modalTitle').text('Editar Usuario');
                $('#modalUsuario').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", xhr.responseText);
            Swal.fire('Error', 'No se pudieron obtener los datos del usuario.', 'error');
        }
    });
}

// 2. FUNCIÓN GLOBAL: Abrir modal limpio para crear
function nuevoUsuario() {
    $('#formUsuario')[0].reset();
    $('#usuario_id').val('');
    $('#modalTitle').text('Nuevo Usuario');
    $('#modalUsuario').modal('show');
}

// 3. EVENTOS DE CARGA (DataTables y envíos de formularios)
document.addEventListener("DOMContentLoaded", function () {

    // Inicialización de DataTables
    if ($.fn.DataTable.isDataTable('#tablaUsuarios')) {
        $('#tablaUsuarios').DataTable().destroy();
    }

    $('#tablaUsuarios').DataTable({
        "ajax": {
            "url": "index.php?controller=usuario&action=listar",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [{
            "data": "id"
        },
        {
            "data": "nombre"
        },
        {
            "data": "mail"
        },
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

    // Envío del formulario por AJAX (Crea o Edita automáticamente)
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
// Debe estar libre en la parte superior del archivo
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
