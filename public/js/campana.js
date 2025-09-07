document.addEventListener('DOMContentLoaded', () => {
  // Inicializar tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  // Inicializar DataTables
  $('#campanasTable').DataTable({
    pageLength: 10,
    order: [[0, 'desc']],
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
    }
  });

  const modalBody = document.getElementById('detalleMedios');
  const detalleModal = new bootstrap.Modal(document.getElementById('detalleCampanaModal'));

  // Botón detalle campaña
  document.querySelectorAll('.btn-detalle').forEach(btn => {
    btn.addEventListener('click', async () => {
      const idcampania = btn.dataset.id;

      // Placeholder antes de cargar
      document.getElementById('detalleNombre').textContent = 'Cargando...';
      document.getElementById('detalleDescripcion').textContent = '';
      document.getElementById('detalleFechas').textContent = '';
      document.getElementById('detallePresupuesto').textContent = '';
      document.getElementById('detalleEstado').textContent = '';
      modalBody.innerHTML = '<tr><td colspan="3">Cargando...</td></tr>';

      detalleModal.show();

      try {
        const res = await fetch(`${BASE_URL}campana/detalle/${idcampania}`);
        if (!res.ok) throw new Error('Error al cargar los datos');

        const data = await res.json();

        // Llenar info general
        document.getElementById('detalleNombre').textContent = data.campana.nombre;
        document.getElementById('detalleDescripcion').textContent = data.campana.descripcion;
        document.getElementById('detalleFechas').textContent = `${data.campana.fecha_inicio} - ${data.campana.fecha_fin}`;
        document.getElementById('detallePresupuesto').textContent = parseFloat(data.campana.presupuesto).toFixed(2);
        document.getElementById('detalleEstado').textContent = data.campana.estado;

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

  // Botón activar/inactivar campaña
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
          // Cambiar texto y color del botón
          btn.textContent = data.estado;
          btn.dataset.estado = data.estado === 'Activo' ? 'Inactivo' : 'Activo';
          btn.classList.toggle('btn-success', data.estado === 'Activo');
          btn.classList.toggle('btn-secondary', data.estado === 'Inactivo');

          actualizarDashboard(); // refresca resumen/grafico
        } else {
          alert('No se pudo cambiar el estado');
        }
      } catch (err) {
        console.error(err);
        alert('Error en la conexión');
      }
    });
  });

  // Función para refrescar resumen/grafico
  function actualizarDashboard() {
    fetch(`${BASE_URL}campana/resumen`)
        .then(res => res.json())
        .then(data => {
        document.querySelector('#cardCampanasActivas').textContent = data.activas ?? 0;
        })
        .catch(err => {
        console.error('Error al actualizar resumen:', err);
        document.querySelector('#cardCampanasActivas').textContent = '0';
        });
    }
});
