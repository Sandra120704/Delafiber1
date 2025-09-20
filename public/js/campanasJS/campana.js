document.addEventListener('DOMContentLoaded', () => {
  console.log('Campañas JS cargado correctamente');
  
  // Inicializar tooltips de Bootstrap
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  // Referencias globales
  const modalBody = document.getElementById('detalleMedios');
  const detalleModal = new bootstrap.Modal(document.getElementById('detalleCampanaModal'));
  const createModal = new bootstrap.Modal(document.getElementById('createCampaignModal'));


  const createForm = document.getElementById('createCampaignForm');
  if (createForm) {
    createForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      console.log('Enviando formulario de nueva campaña...');
      
      const formData = new FormData(createForm);
      
      // Mostrar loading
      const submitBtn = createForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Creando...';
      submitBtn.disabled = true;
      
      try {
        const response = await fetch(`${BASE_URL}campanas/guardar`, {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          // Mostrar mensaje de éxito
          Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: result.message || 'Campaña creada exitosamente',
            timer: 2000,
            showConfirmButton: false
          });
          
          // Cerrar modal y recargar
          createModal.hide();
          setTimeout(() => location.reload(), 2000);
          
        } else {
          // Mostrar errores
          if (result.errors) {
            let errorText = 'Errores encontrados:\n';
            Object.values(result.errors).forEach(error => {
              errorText += `• ${error}\n`;
            });
            alert(errorText);
          } else {
            alert('Error: ' + (result.message || 'Error desconocido'));
          }
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión al crear la campaña');
      } finally {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    });
  }

  document.querySelectorAll('.btn-detalle').forEach(btn => {
    btn.addEventListener('click', async () => {
      const idcampania = btn.dataset.id;
      console.log('Cargando detalle de campaña:', idcampania);

      // Placeholders mientras carga
      const placeholders = {
        detalleNombre: 'Cargando...',
        detalleDescripcion: 'Cargando...',
        detalleFechas: 'Cargando...',
        detallePresupuesto: '0.00',
        detalleEstado: 'Cargando...',
        detalleResponsable: 'Cargando...'
      };
      
      for (const id in placeholders) {
        const el = document.getElementById(id);
        if (el) el.textContent = placeholders[id];
      }
      
      if (modalBody) {
        modalBody.innerHTML = '<tr><td colspan="3" class="text-center">Cargando...</td></tr>';
      }
      
      detalleModal.show();

      try {
        const res = await fetch(`${BASE_URL}campanas/detalle/${idcampania}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}: Error al cargar los datos`);

        const data = await res.json();
        console.log('Datos recibidos:', data);
        
        if (!data || !data.success || !data.campana) {
          if (modalBody) {
            modalBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">No se encontraron datos de la campaña</td></tr>';
          }
          return;
        }

        const campana = data.campana;
        
        // Actualizar información general
        const mapIds = {
          detalleNombre: campana.nombre || 'Sin nombre',
          detalleDescripcion: campana.descripcion || 'Sin descripción',
          detalleFechas: campana.fecha_inicio && campana.fecha_fin 
            ? `${formatDate(campana.fecha_inicio)} - ${formatDate(campana.fecha_fin)}` 
            : 'Fechas no definidas',
          detallePresupuesto: parseFloat(campana.presupuesto || 0).toFixed(2),
          detalleEstado: campana.estado || 'Sin estado',
          detalleResponsable: campana.responsable_nombre || 'No asignado'
        };
        
        for (const id in mapIds) {
          const el = document.getElementById(id);
          if (el) el.textContent = mapIds[id];
        }

        // Llenar tabla de medios
        if (!data.medios || data.medios.length === 0) {
          if (modalBody) {
            modalBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No hay medios registrados</td></tr>';
          }
        } else {
          if (modalBody) {
            modalBody.innerHTML = '';
            data.medios.forEach(item => {
              const roi = item.presupuesto > 0 ? ((item.leads_generados || 0) / item.presupuesto * 100).toFixed(1) : 0;
              modalBody.innerHTML += `
                <tr>
                  <td>
                    <strong>${item.nombre || 'Sin nombre'}</strong>
                    <br><small class="text-muted">${item.medio_descripcion || ''}</small>
                  </td>
                  <td>
                    S/ ${parseFloat(item.presupuesto || 0).toFixed(2)}
                    <br><small class="text-muted">ROI: ${roi}%</small>
                  </td>
                  <td>
                    <span class="badge bg-primary">${item.leads_generados || 0}</span>
                  </td>
                </tr>`;
            });
          }
        }
      } catch (err) {
        console.error('Error al cargar detalle:', err);
        if (modalBody) {
          modalBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error al cargar los datos</td></tr>';
        }
      }
    });
  });

  document.querySelectorAll('.toggle-estado').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const nuevoEstado = btn.dataset.estado;
      const estadoActual = btn.textContent.trim();

      console.log(`Cambiando estado de campaña ${id}: ${estadoActual} → ${nuevoEstado}`);

      // Deshabilitar botón temporalmente
      const originalText = btn.textContent;
      btn.textContent = 'Cambiando...';
      btn.disabled = true;

      try {
        const res = await fetch(`${BASE_URL}campanas/estado/${id}`, {
          method: 'POST',
          headers: { 
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: `estado=${nuevoEstado}`
        });

        console.log('Response status:', res.status);
        console.log('Response headers:', res.headers);

        if (!res.ok) {
          throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }

        const data = await res.json();
        console.log('Respuesta cambio estado:', data);

        if (data.success) {
          // Actualizar botón
          btn.textContent = data.estado;
          btn.dataset.estado = data.estado === 'Activa' ? 'Inactiva' : 'Activa';
          
          // Actualizar clases CSS
          btn.classList.remove('btn-success', 'btn-secondary');
          btn.classList.add(data.estado === 'Activa' ? 'btn-success' : 'btn-secondary');

          // Actualizar dashboard
          actualizarDashboard();
          
          // Mostrar notificación
          showNotification('success', `Estado cambiado a ${data.estado}`);
        } else {
          btn.textContent = originalText;
          console.error('Error del servidor:', data);
          showNotification('error', 'No se pudo cambiar el estado: ' + (data.message || 'Error desconocido'));
        }
      } catch (err) {
        console.error('Error al cambiar estado:', err);
        btn.textContent = originalText;
        
        if (err.message.includes('404')) {
          showNotification('error', 'Ruta no encontrada. Verifique la configuración de rutas.');
        } else if (err.message.includes('500')) {
          showNotification('error', 'Error interno del servidor. Revise los logs.');
        } else {
          showNotification('error', 'Error en la conexión: ' + err.message);
        }
      } finally {
        btn.disabled = false;
      }
    });
  });


  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const row = btn.closest('tr');
      const nombreCampana = row.querySelector('td .fw-bold')?.textContent || 'esta campaña';

      if (!confirm(`¿Está seguro de eliminar "${nombreCampana}"?\n\nEsta acción no se puede deshacer.`)) {
        return;
      }

      console.log('Eliminando campaña:', id);

      try {
        const response = await fetch(`${BASE_URL}campanas/eliminar/${id}`, {
          method: 'DELETE',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const result = await response.json();

        if (result.success) {
          // Animar eliminación de fila
          row.style.transition = 'opacity 0.5s ease';
          row.style.opacity = '0.5';
          
          setTimeout(() => {
            row.remove();
            actualizarDashboard();
            showNotification('success', result.message || 'Campaña eliminada');
          }, 500);
        } else {
          alert('Error al eliminar: ' + (result.message || 'Error desconocido'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión al eliminar la campaña');
      }
    });
  });

  const searchFilter = document.getElementById('searchFilter');
  const statusFilter = document.getElementById('statusFilter');
  const responsableFilter = document.getElementById('responsableFilter');
  const startDate = document.getElementById('startDate');
  const endDate = document.getElementById('endDate');
  const applyFiltersBtn = document.getElementById('applyFilters');

  if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener('click', () => {
      console.log('Aplicando filtros...');
      
      const filtros = {
        search: searchFilter?.value || '',
        estado: statusFilter?.value || '',
        responsable: responsableFilter?.value || '',
        fecha_inicio: startDate?.value || '',
        fecha_fin: endDate?.value || ''
      };

      console.log('Filtros aplicados:', filtros);
      aplicarFiltros(filtros);
    });
  }

  const exportBtn = document.getElementById('exportBtn');
  if (exportBtn) {
    exportBtn.addEventListener('click', () => {
      console.log('Exportando datos...');
      window.open(`${BASE_URL}campanas/exportar?format=csv`, '_blank');
    });
  }

  
  function actualizarDashboard() {
    console.log('Actualizando dashboard...');
    
    fetch(`${BASE_URL}campanas/resumen`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.data) {
          const metricas = data.data;
          
          // Actualizar cards de métricas
          const updates = {
            cardTotalCampanas: metricas.total_campanas || 0,
            cardCampanasActivas: metricas.activas || 0,
            cardPresupuestoTotal: `S/ ${parseInt(metricas.presupuesto_total || 0).toLocaleString()}`,
            cardTotalLeads: metricas.total_leads || 0
          };
          
          for (const [elementId, value] of Object.entries(updates)) {
            const element = document.getElementById(elementId);
            if (element) {
              element.textContent = value;
            }
          }
          
          console.log('Dashboard actualizado correctamente');
        }
      })
      .catch(err => console.error('Error al actualizar dashboard:', err));
  }

  function aplicarFiltros(filtros) {
    const table = $('#campaignsTable').DataTable();
    
    // Aplicar filtros globales
    let searchTerm = filtros.search;
    if (filtros.estado) searchTerm += ` ${filtros.estado}`;
    
    table.search(searchTerm).draw();
  }

  function formatDate(dateString) {
    if (!dateString) return 'No definida';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-PE', {
      day: '2-digit',
      month: '2-digit', 
      year: 'numeric'
    });
  }

  function showNotification(type, message) {
    // Implementación simple de notificaciones
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: type,
        title: type === 'success' ? '¡Éxito!' : 'Error',
        text: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        allowEscapeKey: true,
        allowOutsideClick: true,
        showClass: {
          popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
          popup: 'animate__animated animate__fadeOutUp'
        }
      });
    } else {
      alert(message);
    }
  }

  if (document.getElementById('campaignsTable')) {
    $('#campaignsTable').DataTable({
      responsive: true,
      pageLength: 10,
      order: [[0, 'desc']],
      language: {
        search: "Buscar:",
        lengthMenu: "Mostrar _MENU_ registros por página",
        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        infoEmpty: "No hay registros disponibles",
        infoFiltered: "(filtrado de _MAX_ registros totales)",
        zeroRecords: "No se encontraron registros coincidentes",
        paginate: {
          first: "Primero",
          last: "Último",
          next: "Siguiente",
          previous: "Anterior"
        },
        loadingRecords: "Cargando...",
        processing: "Procesando..."
      },
      dom: '<"d-flex justify-content-between"lf>rtip',
      columnDefs: [
        { 
          targets: -1, 
          orderable: false,
          searchable: false
        }
      ]
    });
  }

  setInterval(() => {
    actualizarDashboard();
  }, 300000); // 5 minutos

  console.log('Inicialización de Campañas completada');
});