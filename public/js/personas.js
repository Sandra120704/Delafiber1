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
        buscando.classList.remove('d-none');
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
                    background: '#9621ccff',
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
                        abrirModalLead(data.idpersona);
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

    // --- Delegación para convertir a lead desde lista ---
    document.addEventListener('click', function(e){
        if(e.target.matches('.btn-converti')){
            const idpersona = e.target.dataset.id;
            abrirModalLead(idpersona);
        }
    });
    async function abrirModalLead(idpersona){
        try {
            const res = await fetch(`${base_url}leads/modals/${idpersona}`);
            const html = await res.text();
            modalContainer.innerHTML = html;
            const modalEl = document.getElementById("leadModal");
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            const leadForm = modalEl.querySelector("#leadForm");
            if(!leadForm) throw new Error("No se encontró el formulario del modal");

         leadForm.addEventListener('submit', async function(e){
            e.preventDefault();
            const formData = new FormData(leadForm);

            try {
                const res = await fetch(leadForm.action, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if(data.success){
                    Swal.fire('¡Éxito!', 'Lead registrado correctamente', 'success');
                    modal.hide();
                } else {
                    Swal.fire('Error', data.message || 'No se pudo registrar el lead', 'error');
                }
            } catch(err){
                Swal.fire('Error', 'Error al registrar el lead', 'error');
            }
        });

    } catch (err) {
        console.error("Error al cargar modal:", err);
        Swal.fire('Error', 'No se pudo abrir el modal de Lead', 'error');
    }
}
});
