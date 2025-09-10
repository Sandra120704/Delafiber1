document.addEventListener('DOMContentLoaded', () => {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  const modalBody = document.getElementById('detalleMedios');
  const detalleModal = new bootstrap.Modal(document.getElementById('detalleCampanaModal'));

  document.querySelectorAll('.btn-detalle').forEach(btn => {
    btn.addEventListener('click', async () => {
      const idcampania = btn.dataset.id;

      const placeholders = {
        detalleNombre: 'Cargando...',
        detalleDescripcion: '',
        detalleFechas: '',
        detallePresupuesto: '',
        detalleEstado: '',
        detalleResponsable: 'Cargando...',
        detalleFechaCreacion: 'Cargando...'
      };

      for (const id in placeholders) {
        const el = document.getElementById(id);
        if (el) el.textContent = placeholders[id];
      }

      modalBody.innerHTML = '<tr><td colspan="3">Cargando...</td></tr>';
      detalleModal.show();

      try {
        const res = await fetch(`${BASE_URL}campana/detalle/${idcampania}`);
        if (!res.ok) throw new Error('Error al cargar los datos');

        const data = await res.json();

        if (!data || !data.campana) {
          modalBody.innerHTML = '<tr><td colspan="3">No se encontraron datos de la campaña</td></tr>';
          return;
        }
        const campana = data.campana;
        const mapIds = {
          detalleNombre: campana.nombre,
          detalleDescripcion: campana.descripcion,
          detalleFechas: `${campana.fecha_inicio} - ${campana.fecha_fin}`,
          detallePresupuesto: parseFloat(campana.presupuesto).toFixed(2),
          detalleEstado: campana.estado,
          detalleResponsable: campana.responsable_nombre ?? 'No asignado',
          detalleFechaCreacion: campana.fecha_creacion
        };

        for (const id in mapIds) {
          const el = document.getElementById(id);
          if (el) el.textContent = mapIds[id];
        }

        if (!data.medios || data.medios.length === 0) {
          modalBody.innerHTML = '<tr><td colspan="3">No hay medios registrados</td></tr>';
        } else {
          modalBody.innerHTML = '';
          data.medios.forEach(item => {
            modalBody.innerHTML += `
              <tr>
                <td>${item.nombre}</td>
                <td>S/ ${parseFloat(item.inversion).toFixed(2)}</td>
                <td>${item.leads ?? 0}</td>
              </tr>`;
          });
        }

      } catch (err) {
        console.error(err);
        modalBody.innerHTML = '<tr><td colspan="3">Error al cargar los datos</td></tr>';
      }
    });
  });

  document.querySelectorAll('.toggle-estado').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const nuevoEstado = btn.dataset.estado;

      try {
        const res = await fetch(`${BASE_URL}campana/cambiarEstado/${id}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `estado=${nuevoEstado}`
        });

        const data = await res.json();

        if (data.success) {
          btn.textContent = data.estado;
          btn.dataset.estado = data.estado === 'Activo' ? 'Inactivo' : 'Activo';
          btn.classList.toggle('btn-success', data.estado === 'Activo');
          btn.classList.toggle('btn-secondary', data.estado === 'Inactivo');
          actualizarDashboard();
        } else {
          alert('No se pudo cambiar el estado');
        }
      } catch (err) {
        console.error(err);
        alert('Error en la conexión');
      }
    });
  });

  function actualizarDashboard() {
    fetch(`${BASE_URL}campana/resumen`)
      .then(res => res.json())
      .then(data => {
        const card = document.querySelector('#cardCampanasActivas');
        if (card) card.textContent = data.activas ?? 0;
      })
      .catch(err => {
        console.error('Error al actualizar resumen:', err);
        const card = document.querySelector('#cardCampanasActivas');
        if (card) card.textContent = '0';
      });
  }
});
