import { initLeadsForm } from '../leadsJS/leadsForm.js';
/* import { showConfirm, showError } from '../utils/alerts.js'; */

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('modalContainer');

  // === Eliminar Persona ===
  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      showConfirm('¿Estás seguro?', 'Esta acción eliminará la persona permanentemente.')
        .then(result => {
          if (result.isConfirmed) {
            window.location.href = `${BASE_URL}/personas/eliminar/${id}`;
          }
        });
    });
  });

  // === Convertir a Lead ===
  document.querySelectorAll('.btn-convertir-lead').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;

      fetch(`${BASE_URL}/leads/modals/${id}`)
        .then(res => res.text())
        .then(html => {
          container.innerHTML = html;
          const modalEl = container.querySelector('#leadModal');
          if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Inicializar JS del modal
            initLeadsForm(modalEl);

            // Limpiar modal al cerrarlo
            modalEl.addEventListener('hidden.bs.modal', () => {
              container.innerHTML = '';
            });
          }
        })
        .catch(() => {
          showError('Error', 'No se pudo conectar con el servidor');
        });
    });
  });

  // === Editar Persona ===
  document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;

      fetch(`${BASE_URL}/personas/editar/${id}`)
        .then(res => res.text())
        .then(html => {
          container.innerHTML = html;
          const modalEl = container.querySelector('#editModal');
          if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
          }
        })
        .catch(err => {
          console.error('Error cargando el formulario de edición:', err);
          showError('Error', 'No se pudo cargar el formulario de edición');
        });
    });
  });
});
