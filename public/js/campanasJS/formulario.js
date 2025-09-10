document.addEventListener('DOMContentLoaded', function() {
  const agregarMedioBtn = document.getElementById('agregarMedioBtn');
  const mediosContainer = document.getElementById('mediosContainer');

  agregarMedioBtn.addEventListener('click', function() {
    const nuevoMedioRow = document.querySelector('.medio-row').cloneNode(true);
    nuevoMedioRow.querySelector('select').value = '';
    nuevoMedioRow.querySelector('input').value = '';
    mediosContainer.appendChild(nuevoMedioRow);
    agregarEliminarEvent(nuevoMedioRow);
  });

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

  document.querySelectorAll('.medio-row').forEach(row => {
    agregarEliminarEvent(row);
  });

  const nuevoMedioBtn = document.getElementById('nuevoMedioBtn');
  const modalNuevoMedio = new bootstrap.Modal(document.getElementById('modalNuevoMedio'));

  nuevoMedioBtn.addEventListener('click', function() {
    modalNuevoMedio.show();
  });

  const guardarMedioBtn = document.getElementById('guardarMedioBtn');
  guardarMedioBtn.addEventListener('click', function() {
    const nombre = document.getElementById('nombreMedio').value.trim();
    const descripcion = document.getElementById('descMedio').value.trim();

    if(nombre === '') {
      alert('El nombre del medio es obligatorio');
      return;
    }

    const nuevosSelects = document.querySelectorAll('select[name="medios[]"]');
    nuevosSelects.forEach(select => {
      const option = document.createElement('option');
      option.value = 'nuevo';
      option.textContent = nombre;
      select.appendChild(option);
    });

    document.getElementById('nombreMedio').value = '';
    document.getElementById('descMedio').value = '';
    modalNuevoMedio.hide();

    alert('Medio agregado (simulado). Implementa el guardado en backend.');
  });
});
