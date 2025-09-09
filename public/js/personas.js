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
        const dni = dniInput.value.trim();

        if (!dni) {
            Swal.fire({
                icon: 'warning',
                text: 'Ingrese un DNI',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
            return;
        }

        buscando.classList.remove('d-none'); // Mostrar loader/spinner

        try {
            const res = await fetch(`${base_url}api/personas/buscardni/${dni}`);
            if (!res.ok) throw new Error('Error en la solicitud');

            const data = await res.json();
            buscando.classList.add('d-none'); // Ocultar loader

            if (data.success) {
                // Llenar los campos con los datos
                apellidosInput.value = `${data.apepaterno} ${data.apematerno}`;
                nombresInput.value = data.nombres;
            } else {
                // Limpiar campos si no se encuentra
                apellidosInput.value = '';
                nombresInput.value = '';

                // Mostrar el toast personalizado
                Swal.fire({
                    text: data.message || 'No se encontró la persona',
                    showConfirmButton: false,
                    icon: 'info',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#2ecc71',
                    iconColor: '#ecf0f1',
                    color: '#fff'
                });
            }
        } catch (err) {
            buscando.classList.add('d-none');
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Error al consultar el DNI',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
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
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'No podrás revertir esto.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });
            } else {
                const mensaje = typeof data.message === 'object'
                    ? Object.values(data.message).join('<br>')
                    : data.message;

                Swal.fire({
                    title: 'Error al registrar',
                    html: mensaje,
                    icon: 'error'
                });
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'Error al registrar la persona', 'error');
        }
    });

    // --- Abrir modal para convertir a lead ---
    document.querySelectorAll(".btn-convertir-lead").forEach(btn => {
        btn.addEventListener("click", function () {
            const idpersona = this.getAttribute("data-id");

            fetch(`${base_url}leads/crear/${idpersona}`)
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

