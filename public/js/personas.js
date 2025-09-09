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

        buscando.classList.remove('d-none'); // Mostrar spinner

        try {
            const res = await fetch(`${base_url}personas/buscadordni/${dni}`);
            const data = await res.json();
            buscando.classList.add('d-none');

            if (data.success) {
                apellidosInput.value = `${data.apepaterno} ${data.apematerno}`;
                nombresInput.value = data.nombres;
            } else {
                apellidosInput.value = '';
                nombresInput.value = '';
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

    // --- Guardar Persona ---
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
                    title: 'Persona registrada',
                    text: '¿Deseas convertirla en lead?',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, convertir',
                    cancelButtonText: 'No por ahora'
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = `${base_url}leads/crear/${data.idpersona}`;
                    }
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

    // --- Convertir a lead desde la lista ---
    document.querySelectorAll(".btn-convertir-lead").forEach(btn => {
        btn.addEventListener("click", function () {
            const idpersona = this.getAttribute("data-id");

            fetch(`${base_url}leads/crear/${idpersona}`)
                .then(res => res.text())
                .then(html => {
                    modalContainer.innerHTML = html;
                    const modal = new bootstrap.Modal(document.getElementById("leadModal"));
                    modal.show();
                })
                .catch(err => console.error("Error al cargar modal:", err));
        });
    });
});

