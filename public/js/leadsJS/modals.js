document.addEventListener('DOMContentLoaded', () => {

  document.querySelectorAll(".btn-convertir-lead").forEach(btn => {
    btn.addEventListener("click", function() {
      const idpersona = this.dataset.id;

      fetch(`${BASE_URL}personas/modalCrear/${idpersona}`) // Usa la ruta correcta
    .then(res => {
        if (!res.ok) throw new Error(`Error ${res.status}: ${res.statusText}`);
        return res.text();
    })
    .then(html => {
        const modal = document.getElementById("leadModal");
        modal.innerHTML = html;
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();

        // Inicializa la interactividad del modal
        if (typeof window.initLeadModalInteractivity === 'function') {
            window.initLeadModalInteractivity(modal);
        }
        // Si usas leads-modal.js, asegúrate que exponga una función global para inicializar

        // Inicializar buscador/autocompletado de DNI si existe
        const dniInput = modal.querySelector('#dni');
        if (dniInput && typeof initDniAutocomplete === 'function') {
          initDniAutocomplete(dniInput);
        }

        const etapaBtns = modal.querySelectorAll('.etapa-btn');
        const etapaInput = modal.querySelector('#idetapa');
            const etapaSelect = modal.querySelector('#etapaSelect');
            if (etapaSelect) {
              etapaSelect.addEventListener('change', () => {
                const selectedValue = etapaSelect.value;
                // Aquí puedes agregar lógica adicional si es necesario
              });
          }

          // Inicializar JS del modal (origen, campañas, etc.)
          if (typeof initLeadsForm === 'function') {
            initLeadsForm(modal);
          }

          // Validación y guardado del formulario del Lead
          const formLead = modal.querySelector('#form-lead') || modal.querySelector('#leadForm');
          if (formLead) {
            formLead.addEventListener('submit', function(e){
              e.preventDefault();

                if (etapaSelect && !etapaSelect.value) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Seleccione una etapa',
                });
                return;
              }

              const formData = new FormData(formLead);

              fetch(`${BASE_URL}leads/guardar`, { // <-- quita la barra inicial si BASE_URL ya la tiene
                method: 'POST',
                body: formData
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  bootstrapModal.hide();

                  // Crear tarjeta Kanban
                  const persona = data.persona;
                    const column = document.querySelector(`#kanban-column-${etapaSelect ? etapaSelect.value : ''}`);

                  const leadCard = document.createElement('div');
                  leadCard.classList.add('kanban-card');
                  leadCard.id = `kanban-card-${data.idlead}`;
                  leadCard.setAttribute('data-id', data.idlead);
                  leadCard.setAttribute('draggable', 'true');
                  leadCard.style.borderLeft = '5px solid #007bff';
                  leadCard.innerHTML = `
                    <div class="card-title">${persona.nombres} ${persona.apellidos}</div>
                    <div class="card-info">
                      <small>${persona.telefono} | ${persona.correo}</small>
                    </div>
                  `;
                  if (column) column.appendChild(leadCard);

                  if (typeof enableDragAndDrop === 'function') enableDragAndDrop();
                  formLead.reset();

                  Swal.fire({
                    icon: 'success',
                    title: 'Lead registrado y agregado al Kanban',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                  });

                } else {
                  Swal.fire('Error', data.message, 'error');
                }
              })
              .catch(() => Swal.fire('Error', 'No se pudo conectar con el servidor', 'error'));
            });
          } else {
            console.error('No se encontró el formulario de lead en el modal.');
          }

          // Limpiar modal al cerrarlo
          modal.addEventListener('hidden.bs.modal', () => {
            modal.querySelector('.modal-content').innerHTML = '';
          });

        })
        .catch(err => {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: `No se pudo abrir el modal de Lead\n${err.message}`,
            confirmButtonText: 'OK'
          });
        });
    });
  });

});
