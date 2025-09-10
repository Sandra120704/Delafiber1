

function enableDragAndDrop(){
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('dragstart', e => {
            e.dataTransfer.setData('text/plain', card.dataset.id);
        });
    });

    document.querySelectorAll('.kanban-column').forEach(col => {
        col.addEventListener('dragover', e => e.preventDefault());
        col.addEventListener('drop', e => {
            const id = e.dataTransfer.getData('text/plain');
            const card = document.getElementById(`kanban-card-${id}`);
            col.appendChild(card);

            // Actualizar etapa en DB
            $.post(`${base_url}/lead/actualizarEtapa`, { idlead: id, idetapa: col.dataset.etapa }, function(res){
                Swal.fire({
                    icon: res.success ? 'success' : 'error',
                    title: res.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
            }, "json");
        });
    });
}

// Inicializar drag & drop
$(document).ready(function(){
    enableDragAndDrop();
});
