document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formCrearLead');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/leads/guardar', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                alert('Lead creado correctamente');
                // Opcional: recargar Kanban o cerrar modal
                location.reload();
            } else {
                alert('Error al crear lead');
            }
        })
        .catch(err => console.error(err));
    });
});
