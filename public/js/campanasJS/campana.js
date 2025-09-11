document.addEventListener('DOMContentLoaded', () => {
  // Inicializar tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  // Referencias
  const modalBody = document.getElementById('detalleMedios');
  const detalleModal = new bootstrap.Modal(document.getElementById('detalleCampanaModal'));

  // Mostrar detalle de campaña
  document.querySelectorAll('.btn-detalle').forEach(btn => {
    btn.addEventListener('click', async () => {
      const idcampania = btn.dataset.id;

      // Placeholders mientras carga
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
          detalleFechaCreacion: campana.fecha_creacion,
          detalleSegmento: campana.segmento ?? 'No definido',
          detalleObjetivos: campana.objetivos ?? 'No definidos',
          detalleNotas: campana.notas ?? 'Sin notas'
        };
        for (const id in mapIds) {
          const el = document.getElementById(id);
          if (el) el.textContent = mapIds[id];
        }

        // Llenar tabla de medios
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

  // Toggle de estado de campaña
  document.querySelectorAll('.toggle-estado').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const nuevoEstado = btn.dataset.estado;

      try {
        const res = await fetch(`${BASE_URL}campana/estado/${id}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `estado=${nuevoEstado}`
        });
        const data = await res.json();

        if (data.success) {
          // Actualizar botón
          btn.textContent = data.estado;
          btn.dataset.estado = data.estado === 'Activo' ? 'Inactivo' : 'Activo';
          btn.classList.toggle('btn-success', data.estado === 'Activo');
          btn.classList.toggle('btn-secondary', data.estado === 'Inactivo');

          // Actualizar dashboard dinámicamente
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

  // Actualizar dashboard en tiempo real
  function actualizarDashboard() {
    fetch(`${BASE_URL}campana/resumen`)
      .then(res => res.json())
      .then(data => {
        const cards = {
          cardTotalCampanas: data.total_campanas ?? 0,
          cardCampanasActivas: data.activas ?? 0,
          cardPresupuestoTotal: parseFloat(data.presupuesto_total ?? 0).toFixed(2),
          cardTotalLeads: data.total_leads ?? 0
        };
        for (const id in cards) {
          const el = document.getElementById(id);
          if (el) el.textContent = cards[id];
        }
      })
      .catch(err => console.error('Error al actualizar resumen:', err));
  }

  // Inicializar DataTable responsive
  if (document.getElementById('campanasTable')) {
    $('#campanasTable').DataTable({
      responsive: true,
      language: {
        search: "Buscar:",
        lengthMenu: "Mostrar _MENU_ registros",
        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        paginate: { previous: "Anterior", next: "Siguiente" }
      }
    });
  }
});
