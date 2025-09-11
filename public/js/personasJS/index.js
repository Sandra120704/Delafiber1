import { showConfirm, showError } from '../utils/alerts.js';

document.addEventListener('DOMContentLoaded', () => {
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
          const container = document.getElementById('modalContainer');
          container.innerHTML = html;

          const modalEl = document.getElementById('leadModal');
          if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // limpiar modal al cerrarlo
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
          document.getElementById('modalContainer').innerHTML = html;

          const modalEl = document.getElementById('editModal');
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
