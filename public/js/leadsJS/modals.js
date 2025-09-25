document.addEventListener('DOMContentLoaded', () => {

  document.querySelectorAll(".btn-convertir-lead").forEach(btn => {
    btn.addEventListener("click", function() {
      const idpersona = this.dataset.id;

      fetch(`${BASE_URL}leads/crear/${idpersona}`)
        .then(res => res.text())
        .then(html => {
          const modal = document.getElementById("leadModal");
          modal.querySelector('.modal-content').innerHTML = html;
          const bootstrapModal = new bootstrap.Modal(modal);
          bootstrapModal.show();

          // Inicializar selección de etapa dentro del modal
          const etapaBtns = modal.querySelectorAll('.etapa-btn');
          const etapaInput = modal.querySelector('#idetapa');

          etapaBtns.forEach(b => {
            b.addEventListener('click', () => {
              etapaBtns.forEach(btn => btn.classList.remove('active'));
              b.classList.add('active');
              etapaInput.value = b.dataset.etapa;
            });
          });

          // Inicializar JS del modal (origen, campañas, etc.)
          if (typeof initLeadsForm === 'function') {
            initLeadsForm(modal);
          }

          // Validación y guardado del formulario del Lead
          const formLead = modal.querySelector('#form-lead') || modal.querySelector('#leadForm');
          formLead.addEventListener('submit', function(e){
            e.preventDefault();

            if (!etapaInput.value) {
              Swal.fire({
                icon: 'warning',
                title: 'Seleccione una etapa',
              });
              return;
            }

            const formData = new FormData(formLead);

            fetch(`${BASE_URL}/leads/guardar`, {
              method: 'POST',
              body: formData
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                bootstrapModal.hide();

                // Crear tarjeta Kanban
                const persona = data.persona;
                const column = document.querySelector(`#kanban-column-${etapaInput.value}`);

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
                column.appendChild(leadCard);

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

          // Limpiar modal al cerrarlo
          modal.addEventListener('hidden.bs.modal', () => {
            modal.querySelector('.modal-content').innerHTML = '';
          });

        }).catch(err => console.error('Error al cargar modal:', err));
    });
  });

});
