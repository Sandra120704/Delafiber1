document.addEventListener('DOMContentLoaded', () => {
    const dniInput = document.getElementById('dni');
    const btnBuscar = document.getElementById('buscar-dni');
    const apellidosInput = document.getElementById('apellidos');
    const nombresInput = document.getElementById('nombres');
    const buscando = document.getElementById('searching');
    const formPersona = document.getElementById('form-persona');
    const modalContainer = document.getElementById("modalContainer");
    // --- Buscar DNI ---
    btnBuscar.addEventListener('click', async () => {
        if (!dniInput.value) {
            alert('Ingrese un DNI');
            return;
        }
        buscando.classList.remove('d-none');

        try {
            const res = await fetch(`${base_url}api/personas/buscardni/${dniInput.value}`);
            if (!res.ok) throw new Error('Error en la solicitud');

            const data = await res.json();
            buscando.classList.add('d-none');

            if (data.success) {
                apellidosInput.value = `${data.apepaterno} ${data.apematerno}`;
                nombresInput.value = data.nombres;
            } else {
                apellidosInput.value = '';
                nombresInput.value = '';
                alert(data.message || 'No se encontró la persona');
            }
        } catch (err) {
            buscando.classList.add('d-none');
            console.error(err);
            alert('Error al consultar el DNI');
        }
    });

    // --- Guardar Persona y preguntar si convertir a lead ---
    formPersona.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formPersona);

        try {
            const res = await fetch(formPersona.action, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                // Usar SweetAlert para confirmar conversión
                Swal.fire({
                    title: 'Persona registrada',
                    text: '¿Desea convertir a esta persona en un lead?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, convertir a lead',
                    cancelButtonText: 'No, gracias'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `${base_url}leads/registrar/${data.idpersona}`;
                    } else {
                        formPersona.reset();
                    }
                });
            } else {
                Swal.fire('Error', data.message || 'Error al registrar la persona', 'error');
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'Error al registrar la persona', 'error');
        }
    });
    document.querySelectorAll(".btn-convertir-lead").forEach(btn => {
    btn.addEventListener("click", function() {
      const idpersona = this.getAttribute("data-id");

      fetch(`<?= site_url('leads/crear/') ?>/${idpersona}`)
        .then(res => res.text())
        .then(html => {
          modalContainer.innerHTML = html;

          // Inicializar modal de Bootstrap
          const modal = new bootstrap.Modal(document.getElementById("leadModal"));
          modal.show();
        })
        .catch(err => console.error("Error al cargar modal:", err));
    });
  });
});

