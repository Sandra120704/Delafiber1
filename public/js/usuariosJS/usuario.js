const baseUrl = '<?= base_url() ?>';

$(document).ready(function() {
    // Cambiar estado activo/inactivo
    $('.estado-switch').change(function() {
        const usuarioId = $(this).data('id');
        const activo = $(this).is(':checked');
        
        $.post(`${baseUrl}usuarios/cambiarEstado/${usuarioId}`, {
            activo: activo ? 1 : 0
        })
        .done(function(response) {
            if (response.success) {
                const badge = $(this).closest('td').find('.badge');
                badge.removeClass('bg-success bg-secondary')
                     .addClass(activo ? 'bg-success' : 'bg-secondary')
                     .text(activo ? 'Activo' : 'Inactivo');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        }.bind(this));
    });

    // Crear/Editar usuario
    $('#formUsuario').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const usuarioId = $('#idusuario').val();
        const url = usuarioId ? `${baseUrl}usuarios/editar/${usuarioId}` : `${baseUrl}usuarios/crear`;
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success) {
                $('#modalUsuario').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 2000);
            } else {
                Swal.fire('Error', response.message || 'Error al guardar usuario', 'error');
            }
        })
        .fail(function(xhr) {
            console.log('Error:', xhr.responseText);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
    });

    // Eliminar usuario
    $(document).on('click', '.btn-eliminar', function() {
        const usuarioId = $(this).data('id');
        
        Swal.fire({
            title: '¿Eliminar usuario?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}usuarios/eliminar/${usuarioId}`,
                    method: 'DELETE',
                    dataType: 'json'
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Eliminado', 'Usuario eliminado correctamente', 'success');
                        setTimeout(() => location.reload(), 2000);
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'No se pudo eliminar el usuario', 'error');
                });
            }
        });
    });

    // Buscar usuarios
    $('#buscarUsuario').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });

    // Resetear contraseña
    $(document).on('click', '.btn-resetear-password', function() {
        const usuarioId = $(this).data('id');
        
        Swal.fire({
            title: 'Resetear contraseña',
            input: 'password',
            inputLabel: 'Nueva contraseña',
            inputPlaceholder: 'Ingresa la nueva contraseña',
            showCancelButton: true,
            confirmButtonText: 'Cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.post(`${baseUrl}usuarios/resetearPassword/${usuarioId}`, {
                    nueva_password: result.value
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', 'Contraseña actualizada', 'success');
                    }
                });
            }
        });
    });
});

// Filtrar usuarios
function filtrarUsuarios(filtro) {
    // Actualizar botones activos
    $('.btn-group button').removeClass('active');
    event.target.classList.add('active');
    
    $('tbody tr').show();
    
    switch(filtro) {
        case 'activos':
            $('tbody tr[data-estado="inactivo"]').hide();
            break;
        case 'inactivos':
            $('tbody tr[data-estado="activo"]').hide();
            break;
        case 'vendedores':
            $('tbody tr:not([data-rol="vendedor"])').hide();
            break;
        case 'admins':
            $('tbody tr:not([data-rol="admin"])').hide();
            break;
    }
}