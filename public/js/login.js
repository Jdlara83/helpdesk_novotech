$(document).ready(function () {
    $('#formLogin').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'index.php?controller=auth&action=login',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Acceso Correcto!',
                        text: response.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(function () {
                        // Redirigir usando response.redirect o la ruta por defecto al Dashboard
                        window.location.href = response.redirect || 'index.php?controller=dashboard&action=index';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de acceso',
                        text: response.message
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: 'Ocurrió un fallo en la solicitud.'
                });
            }
        });
    });
});