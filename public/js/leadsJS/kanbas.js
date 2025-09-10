document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = "<?= base_url() ?>";

    // Inicializar drag & drop
    function initKanbanCards() {
        const cards = document.querySelectorAll('.kanban-card');
        const columns = document.querySelectorAll('.kanban-column');

        cards.forEach(card => {
            card.setAttribute('draggable', true);

            card.addEventListener('dragstart', e => {
                e.dataTransfer.setData('text/plain', card.dataset.id);
                card.classList.add('dragging');
            });

            card.addEventListener('dragend', e => {
                card.classList.remove('dragging');
            });
        });

        columns.forEach(column => {
            column.addEventListener('dragover', e => e.preventDefault());
            column.addEventListener('drop', e => {
                e.preventDefault();
                const idlead = e.dataTransfer.getData('text/plain');
                const card = document.getElementById('kanban-card-' + idlead);
                column.appendChild(card);

                // Actualizar la etapa en BD via AJAX
                const nuevaEtapa = column.dataset.etapa;
                fetch(`${baseUrl}/lead/mover`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ idlead: idlead, idetapa: nuevaEtapa })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success'){
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: `Lead movido a etapa: ${data.etapa}`,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            });
        });
    }

    initKanbanCards();

    // Función para mostrar toast cuando un lead ya existe
    function showLeadExistsToast() {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Esta persona ya está registrada como Lead.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Exportar funciones si quieres llamar desde otros scripts
    window.initKanbanCards = initKanbanCards;
    window.showLeadExistsToast = showLeadExistsToast;
});
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = "<?= base_url() ?>";

    // ====== Drag & Drop Kanban ======
    function initKanbanCards() {
        const cards = document.querySelectorAll('.kanban-card');
        const columns = document.querySelectorAll('.kanban-column');

        cards.forEach(card => {
            card.setAttribute('draggable', true);

            card.addEventListener('dragstart', e => {
                e.dataTransfer.setData('text/plain', card.dataset.id);
                card.classList.add('dragging');
            });

            card.addEventListener('dragend', e => card.classList.remove('dragging'));
        });

        columns.forEach(column => {
            column.addEventListener('dragover', e => e.preventDefault());
            column.addEventListener('drop', e => {
                e.preventDefault();
                const idlead = e.dataTransfer.getData('text/plain');
                const card = document.getElementById('kanban-card-' + idlead);
                column.appendChild(card);

                // Actualizar etapa en DB
                const nuevaEtapa = column.dataset.etapa;
                fetch(`${baseUrl}/lead/mover`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ idlead, idetapa: nuevaEtapa })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success'){
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: `Lead movido a etapa: ${data.etapa}`,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            });
        });
    }

    initKanbanCards();

    // ====== Función para agregar un lead al Kanban dinámicamente ======
    window.agregarLeadKanban = function(lead) {
        const column = document.getElementById('kanban-column-' + lead.idetapa);
        if(!column) return;

        const leadCard = document.createElement('div');
        leadCard.className = 'kanban-card';
        leadCard.id = 'kanban-card-' + lead.idlead;
        leadCard.dataset.id = lead.idlead;
        leadCard.draggable = true;
        leadCard.style.cssText = 'border-left:5px solid #007bff; margin-bottom:10px; padding:8px; background:#fff; border-radius:6px;';
        leadCard.innerHTML = `
            <div class="card-title">${lead.nombres} ${lead.apellidos}</div>
            <div class="card-info">
                ${lead.telefono}<br>
                ${lead.correo}<br>
                ${lead.campania || ''} - ${lead.medio || ''}<br>
                Usuario: ${lead.usuario || ''}
            </div>
        `;

        column.appendChild(leadCard);
        initKanbanCards();

        Swal.fire({
            toast: true,
            icon: 'success',
            title: 'Lead agregado al Kanban',
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    }
});
