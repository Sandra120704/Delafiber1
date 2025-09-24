export function initLeadsForm(modalEl) {
    const origenSelect = modalEl.querySelector('#origenSelect');
    const campaniaDiv = modalEl.querySelector('#campaniaDiv');
    const campSelect = modalEl.querySelector('#campaniaSelect');
    const referenteDiv = modalEl.querySelector('#referenteDiv');
    const referidoInput = modalEl.querySelector('#referido_por');
    const form = modalEl.querySelector('#leadForm');

    if (!origenSelect || !form) return;

    // Colores por estado
    const colorEstado = {
        'Nuevo': '#007bff',
        'En proceso': '#ffc107',
        'Convertido': '#28a745',
        'Descartado': '#6c757d'
    };

    // --- Función para mostrar/ocultar divs según origen ---
    function actualizarDivs() {
        const tipo = origenSelect.options[origenSelect.selectedIndex]?.getAttribute('data-tipo') || '';

        if (tipo === 'campaña' || tipo === 'campania') {
            campaniaDiv.style.display = 'block';
            referenteDiv.style.display = 'none';
            campSelect.required = true;
            referidoInput.required = false;
        } else if (tipo === 'referido') {
            campaniaDiv.style.display = 'none';
            referenteDiv.style.display = 'block';
            campSelect.required = false;
            referidoInput.required = true;
        } else {
            campaniaDiv.style.display = 'none';
            referenteDiv.style.display = 'none';
            campSelect.required = false;
            referidoInput.required = false;
        }
    }

    // Inicializar
    actualizarDivs();

    // Evento dinámico
    origenSelect.addEventListener('change', actualizarDivs);

    // --- Manejo del submit del formulario ---
    form.addEventListener('submit', async function(e){
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const res = await fetch(`${BASE_URL}/leads/guardar`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if(data.success){
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                const persona = data.persona;
                const estado = persona.estado || 'Nuevo';
                const color = colorEstado[estado] || '#007bff';

                // Crear tarjeta del Kanban
                const column = document.getElementById('kanban-column-' + persona.idetapa);
                if(column){
                    const leadCard = document.createElement('div');
                    leadCard.className = 'kanban-card';
                    leadCard.id = 'kanban-card-' + data.idlead;
                    leadCard.setAttribute('data-id', data.idlead);
                    leadCard.setAttribute('draggable', 'true');
                    leadCard.style.borderLeft = `5px solid ${color}`;
                    leadCard.innerHTML = `
                        <div class="card-title">${persona.nombres} ${persona.apellidos}</div>
                        <div class="card-info">
                            <small>${persona.telefono || ''} | ${persona.correo || ''}</small>
                        </div>
                    `;
                    column.appendChild(leadCard);

                    if(typeof enableDragAndDrop === 'function') enableDragAndDrop();
                }

                // Limpiar formulario
                form.reset();
                campaniaDiv.style.display = 'none';
                referenteDiv.style.display = 'none';
                campSelect.required = false;
                referidoInput.required = false;

                Swal.fire({
                    icon: 'success',
                    title: 'Lead registrado',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch(err){
            console.error(err);
            Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
        }
    });

    // Limpiar modal al cerrarlo
    modalEl.addEventListener('hidden.bs.modal', () => {
        campaniaDiv.style.display = 'none';
        referenteDiv.style.display = 'none';
        campSelect.required = false;
        referidoInput.required = false;
        origenSelect.value = '';
        campSelect.value = '';
        referidoInput.value = '';
        form.reset();
    });
}
