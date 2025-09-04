document.addEventListener('DOMContentLoaded', () => {
    // Envío de formulario de creación de lead
    const form = document.getElementById('formCrearLead');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?= base_url("leads/guardar") ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                alert('Lead creado correctamente');
                location.reload(); // Recarga la Kanban para mostrar el nuevo lead
            } else {
                alert('Error al crear lead');
            }
        })
        .catch(err => console.error(err));
    });

    // Click en card lead (modal detalles)
    document.querySelectorAll('.lead-card').forEach(card => {
        card.addEventListener('click', () => {
            const idlead = card.dataset.idlead;
            fetch('<?= base_url("leads/detalle") ?>/' + idlead)
                .then(resp => resp.text())
                .then(html => {
                    const modalBody = document.querySelector('#modalDetalleLead .modal-body');
                    modalBody.innerHTML = html;
                    new bootstrap.Modal(document.getElementById('modalDetalleLead')).show();
                });
        });
    });
});
