// URLs desde PHP
const base_url = "<?= site_url('') ?>";
const leadCrearUrl = "<?= site_url('lead/crear') ?>";
const leadGuardarUrl = "<?= site_url('lead/guardar') ?>";

$(document).ready(function() {

    // ===== Abrir modal "Convertir en Lead" =====
    $(document).on('click', '.btn-convert', function() {
        const idpersona = $(this).data('id');

        $.get(leadCrearUrl, { idpersona: idpersona })
        .done(function(html) {
            $('#modalContainer').html(html); // inyecta el modal

            const modalEl = document.getElementById('modalLead');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // ===== Guardar Lead desde el modal =====
            $('#formLead').off('submit').on('submit', function(e) {
                e.preventDefault();
                const data = $(this).serialize();

                $.post(leadGuardarUrl, data, function(response) {
                    if(response.success) {
                        window.location.href = base_url + 'lead/kanban';
                    } else {
                        alert('Error al crear el Lead');
                    }
                }, 'json').fail(function(xhr, status, error) {
                    console.error('AJAX Error:', status, error, xhr.responseText);
                    alert('Error en la conexión al servidor');
                });
            });

        })
        .fail(function(xhr, status, error) {
            console.error('AJAX Error al cargar modal:', status, error, xhr.responseText);
            alert('No se pudo cargar el modal de Lead');
        });
    });

});
