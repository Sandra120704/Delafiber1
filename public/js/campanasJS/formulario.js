document.addEventListener('DOMContentLoaded', function() {
  const agregarMedioBtn = document.getElementById('agregarMedioBtn');
  const mediosContainer = document.getElementById('mediosContainer');

  // Función para agregar evento eliminar
  function agregarEliminarEvent(medioRow) {
    const btnEliminar = medioRow.querySelector('.eliminarMedio');
    btnEliminar.addEventListener('click', function() {
      if(document.querySelectorAll('.medio-row').length > 1) {
        medioRow.remove();
      } else {
        alert('Debe haber al menos un medio');
      }
    });
  }

  // Inicializar eventos eliminar en los medios existentes
  document.querySelectorAll('.medio-row').forEach(row => agregarEliminarEvent(row));

  // Agregar nueva fila de medio
  agregarMedioBtn.addEventListener('click', function() {
    const template = document.querySelector('.medio-row');
    const nuevoMedioRow = template.cloneNode(true);
    nuevoMedioRow.querySelectorAll('input, select').forEach(el => el.value = '');
    mediosContainer.appendChild(nuevoMedioRow);
    agregarEliminarEvent(nuevoMedioRow);
  });

  // Modal para agregar medio nuevo
  const modalNuevoMedio = new bootstrap.Modal(document.getElementById('modalNuevoMedio'));
  document.getElementById('nuevoMedioBtn').addEventListener('click', () => modalNuevoMedio.show());

  // Guardar medio desde modal y actualizar selects
  document.getElementById('guardarMedioBtn').addEventListener('click', async function() {
    const nombre = document.getElementById('nombreMedio').value.trim();
    const descripcion = document.getElementById('descMedio').value.trim();

    if(nombre === '') {
      alert('El nombre del medio es obligatorio');
      return;
    }

    try {
      const formData = new FormData();
      formData.append('nombre', nombre);
      formData.append('descripcion', descripcion);

      const res = await fetch(`${BASE_URL}medio/guardar`, {
        method: 'POST',
        body: formData
      });

      const data = await res.json();
      if(!data.success) throw new Error(data.message || 'Error al guardar medio');

      // Agregar nuevo medio a todos los selects
      document.querySelectorAll('select[name="medios[]"]').forEach(select => {
        const option = document.createElement('option');
        option.value = data.id; // id real del medio
        option.textContent = nombre;
        select.appendChild(option);
      });

      // Limpiar inputs y cerrar modal
      document.getElementById('nombreMedio').value = '';
      document.getElementById('descMedio').value = '';
      modalNuevoMedio.hide();

    } catch(err) {
      console.error(err);
      alert('No se pudo guardar el medio. Intente nuevamente.');
    }
  });
});
