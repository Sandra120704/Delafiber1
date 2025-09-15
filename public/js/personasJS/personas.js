document.addEventListener('DOMContentLoaded', () => {
    const dniInput = document.getElementById('dni');
    const btnBuscar = document.getElementById('buscar-dni');
    const apellidosInput = document.getElementById('apellidos');
    const nombresInput = document = document.getElementById('nombres');
    const buscando = document.getElementById('searching');
    const formPersona = document.getElementById('form-persona');
    const modalContainer = document.getElementById("modalContainer");
    const inputBuscar = document.getElementById("buscar-persona");
    const tablaPersonas = document.getElementById("tabla-personas");

    // Asegura que estas funciones existan
    if (typeof activarBotonesEliminar === 'function') {
        activarBotonesEliminar();
    }
    if (typeof activarBotonesEditar === 'function') {
        activarBotonesEditar();
    }
    if (typeof activarBotonesConvertirLead === 'function') {
        activarBotonesConvertirLead();
    }

    btnBuscar?.addEventListener('click', async () => {
        const dni = dniInput.value.trim();
        if (!dni || dni.length !== 8 || isNaN(dni)) {
            Swal.fire({ icon: 'warning', text: 'Ingrese un DNI válido de 8 dígitos', toast: true, position: 'bottom-end', timer: 3000, showConfirmButton: false });
            return;
        }

        buscando.classList.remove('d-none');
        btnBuscar.disabled = true;

        try {
            const res = await fetch(`${BASE_URL}personas/buscardni/${dni}`);
            if (res.status === 404) {
                apellidosInput.value = '';
                nombresInput.value = '';
                Swal.fire({ icon: 'info', text: 'No se encontró la persona con ese DNI', toast: true, position: 'bottom-end', timer: 3000, showConfirmButton: false });
                return;
            }
            if (!res.ok) throw new Error('Error en la consulta');

            const data = await res.json();
            if (data.success) {
                apellidosInput.value = `${data.apepaterno} ${data.apematerno}`;
                nombresInput.value = data.nombres;
            } else {
                apellidosInput.value = '';
                nombresInput.value = '';
                Swal.fire({ icon: 'info', text: data.message || 'No se encontró la persona', toast: true, position: 'bottom-end', timer: 3000, showConfirmButton: false });
            }
        } catch {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un error al consultar el DNI', toast: true, position: 'bottom-end', timer: 3000, showConfirmButton: false });
        } finally {
            buscando.classList.add('d-none');
            btnBuscar.disabled = false;
        }
    });

    formPersona?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formPersona);
        const submitBtn = formPersona.querySelector('[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        try {
            const res = await fetch(formPersona.action, { method: 'POST', body: formData });
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
                    if (result.isConfirmed) abrirModalLead(data.idpersona);
                    else if (inputBuscar) inputBuscar.dispatchEvent(new Event("keyup"));
                });
            } else {
                if (data.errors) {
                    const mensajes = Object.values(data.errors).join('<br>');
                    Swal.fire({ title: 'Errores de validación', html: mensajes, icon: 'error' });
                } else {
                    Swal.fire({ title: 'Error', text: data.message, icon: 'error' });
                }
            }
        } catch {
            Swal.fire('Error', 'Error al registrar la persona', 'error');
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });

    document.addEventListener('click', (e) => {
        if (e.target.matches('.btn-convertir-lead') || e.target.closest('.btn-convertir-lead')) {
            const button = e.target.closest('.btn-convertir-lead');
            abrirModalLead(button.dataset.id);
        }
    });

    // Función central para abrir el modal de Lead
    async function abrirModalLead(idpersona) {
        try {
            const res = await fetch(`${BASE_URL}personas/modalCrear/${idpersona}`);
            if (!res.ok) throw new Error(`HTTP error ${res.status}`);
            const html = await res.text();
            modalContainer.innerHTML = html;

            const modalEl = document.getElementById("leadModal");
            if (!modalEl) throw new Error("No se encontró el modal en el HTML");

            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Lógica para manejar el envío del formulario del modal
            const leadForm = modalEl.querySelector("#leadForm");
            if (leadForm) {
                leadForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const submitBtn = leadForm.querySelector('[type="submit"]');
                    if (submitBtn) submitBtn.disabled = true;

                    const url = `${BASE_URL}persona/guardarLead`;
                    const formData = new FormData(leadForm);
                    
                    // --- Inicio de la lógica de corrección del error de llave foránea ---
                    const origenSelect = leadForm.querySelector('#origenSelect');
                    const selectedOption = origenSelect.options[origenSelect.selectedIndex];
                    const tipo = selectedOption ? selectedOption.dataset.tipo : '';

                    // Si el origen no es "Campaña", nos aseguramos de no enviar un valor que viole la llave foránea
                    if (tipo !== 'campaña') {
                        formData.set('idcampania', '');
                    }
                    
                    // Si el origen no es "Referido", nos aseguramos de no enviar un valor que viole la llave foránea
                    if (tipo !== 'referido') {
                        // AQUÍ ESTÁ EL CAMBIO IMPORTANTE: el campo se llama referido_por
                        formData.set('referido_por', '');
                    }
                    // --- Fin de la lógica de corrección ---

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            body: formData
                        });
                        const data = await res.json();

                        if (data.success) {
                            Swal.fire('¡Éxito!', data.message, 'success');
                            modal.hide();
                            location.reload();
                        } else {
                            modalEl.querySelectorAll('.error-message').forEach(el => el.textContent = '');

                            if (data.errors) {
                                for (const field in data.errors) {
                                    const errorEl = modalEl.querySelector(`#${field}-error`);
                                    if (errorEl) {
                                        errorEl.textContent = data.errors[field];
                                    }
                                }
                                Swal.fire('Errores de validación', 'Por favor, revise los campos marcados.', 'error');
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Ocurrió un error al guardar el lead.', 'error');
                    } finally {
                        if (submitBtn) submitBtn.disabled = false;
                    }
                });
            }

            // Lógica para mostrar/ocultar campos del modal
            const origenSelect = modalEl.querySelector('#origenSelect');
            const campaniaDiv = modalEl.querySelector('#campaniaDiv');
            const referidoDiv = modalEl.querySelector('#referidoDiv');

            function toggleConditionalFields() {
                const selectedOption = origenSelect.options[origenSelect.selectedIndex];
                const tipo = selectedOption ? selectedOption.dataset.tipo : '';
                
                campaniaDiv.style.display = 'none';
                referidoDiv.style.display = 'none';
                
                if (tipo === 'campaña') {
                    campaniaDiv.style.display = 'block';
                } else if (tipo === 'referido') {
                    referidoDiv.style.display = 'block';
                }
            }
            
            toggleConditionalFields();
            origenSelect.addEventListener('change', toggleConditionalFields);

        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'No se pudo abrir el modal de Lead', 'error');
        }
    }

    if (inputBuscar) {
        inputBuscar.addEventListener("keyup", async () => {
            const query = inputBuscar.value;
            try {
                const res = await fetch(`${BASE_URL}personas/buscarAjax?q=${query}`);
                const data = await res.json();
                tablaPersonas.innerHTML = '';

                if (data.length === 0) {
                    tablaPersonas.innerHTML = `<tr><td colspan="4" class="text-center">No se encontraron resultados</td></tr>`;
                    return;
                }

                data.forEach(persona => {
                    tablaPersonas.innerHTML += `
                        <tr>
                            <td>${persona.dni}</td>
                            <td>${persona.nombres}</td>
                            <td>${persona.apellidos}</td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-editar" data-id="${persona.idpersona}">Editar</button>
                                <button class="btn btn-sm btn-danger btn-eliminar" data-id="${persona.idpersona}">Eliminar</button>
                                <button class="btn btn-sm btn-success btn-convertir-lead" data-id="${persona.idpersona}">Convertir Lead</button>
                            </td>
                        </tr>
                    `;
                });

                activarBotonesEliminar();
                activarBotonesEditar();
            } catch {
                tablaPersonas.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Error al buscar</td></tr>`;
            }
        });
    }

    function activarBotonesEliminar() {
        document.querySelectorAll(".btn-eliminar").forEach(button => {
            button.addEventListener("click", () => {
                const personaId = button.dataset.id;
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "No podrás revertir esta acción.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch(`${BASE_URL}personas/eliminar/${personaId}`, { method: "POST" })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) Swal.fire("Eliminado!", data.message, "success").then(() => inputBuscar.dispatchEvent(new Event("keyup")));
                                else Swal.fire("Error!", data.message, "error");
                            });
                    }
                });
            });
        });
    }

    function activarBotonesEditar() {
        document.querySelectorAll('.btn-editar').forEach(button => {
            button.addEventListener('click', async () => {
                const personaId = button.dataset.id;
                try {
                    const res = await fetch(`${BASE_URL}personas/editar/${personaId}`);
                    if (!res.ok) throw new Error();
                    const html = await res.text();
                    modalContainer.innerHTML = html;
                    const modalEl = document.getElementById("editModal");
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    const editForm = modalEl.querySelector("#form-editar-persona");
                    if (!editForm) throw new Error();

                    editForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const formData = new FormData(editForm);
                        try {
                            const res = await fetch(editForm.action, { method: 'POST', body: formData });
                            const data = await res.json();
                            if (data.success) {
                                Swal.fire('¡Actualizado!', data.message, 'success').then(() => inputBuscar.dispatchEvent(new Event("keyup")));
                                modal.hide();
                            } else if (data.errors) {
                                const mensajes = Object.values(data.errors).join('<br>');
                                Swal.fire({ title: 'Errores de validación', html: mensajes, icon: 'error' });
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        } catch {
                            Swal.fire('Error!', 'No se pudo actualizar la persona.', 'error');
                        }
                    });
                } catch {
                    Swal.fire('Error', 'No se pudo abrir el modal de edición', 'error');
                }
            });
        });
    }
});
