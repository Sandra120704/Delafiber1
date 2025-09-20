const baseUrl = '<?= base_url() ?>';

$(document).ready(function() {
    // Cambiar estado de tarea
    $('.estado-select').change(function() {
        const tareaId = $(this).data('id');
        const nuevoEstado = $(this).val();
        
        $.ajax({
            url: `${baseUrl}tareas/cambiarEstado/${tareaId}`,
            method: 'POST',
            data: { estado: nuevoEstado },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Estado actualizado correctamente',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 2000);
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText);
                Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
            }
        });
    });

    // Crear/Editar tarea
    $('#formTarea').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const tareaId = $('#idtarea').val();
        const url = tareaId ? `${baseUrl}tareas/editar/${tareaId}` : `${baseUrl}tareas/crear`;
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#modalTarea').modal('hide');
                    Swal.fire({
                        title: '¡Éxito!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 2000);
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText);
                try {
                    const response = JSON.parse(xhr.responseText);
                    Swal.fire('Error', response.message || 'Error al guardar la tarea', 'error');
                } catch(e) {
                    Swal.fire('Error', 'Error al guardar la tarea', 'error');
                }
            }
        });
    });

    // Eliminar tarea
    $(document).on('click', '.btn-eliminar', function() {
        const tareaId = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
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
                    url: `${baseUrl}tareas/eliminar/${tareaId}`,
                    method: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Eliminada', response.message, 'success');
                            setTimeout(() => location.reload(), 2000);
                        }
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseText);
                        Swal.fire('Error', 'No se pudo eliminar la tarea', 'error');
                    }
                });
            }
        });
    });

    // Búsqueda de tareas
    $('#buscarTarea').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });
});

function mostrarCalendario() {
    window.location.href = `${baseUrl}tareas/calendario`;
}