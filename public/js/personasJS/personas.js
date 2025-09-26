/**
 * Gestor de Personas - JavaScript Optimizado
 * Maneja formularios, búsquedas y conversión a leads
 */

class PersonaManager {
    constructor() {
        this.elements = this.getElements();
        this.init();
    }

    getElements() {
        return {
            dniInput: document.getElementById('dni'),
            btnBuscar: document.getElementById('buscar-dni'),
            apellidosInput: document.getElementById('apellidos'),
            nombresInput: document.getElementById('nombres'),
            buscando: document.getElementById('searching'),
            formPersona: document.getElementById('form-persona'),
            modalContainer: document.getElementById('modalContainer'),
            inputBuscar: document.getElementById('buscar-persona'),
            tablaPersonas: document.getElementById('tabla-personas')
        };
    }

    init() {
        this.setupEventListeners();
        this.activarBotonesAccion();
        this.setupFormValidation();
    }

    setupEventListeners() {
        // Búsqueda DNI
        this.elements.btnBuscar?.addEventListener('click', () => this.buscarDNI());
        
        // Enter en campo DNI
        this.elements.dniInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.buscarDNI();
            }
        });

        // Envío de formulario
        this.elements.formPersona?.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Búsqueda con debounce
        if (this.elements.inputBuscar) {
            this.elements.inputBuscar.addEventListener('input', 
                this.debounce(() => this.buscarPersonas(), 300)
            );
        }

        // Delegación de eventos para botones dinámicos
        document.addEventListener('click', (e) => this.handleGlobalClick(e));
    }

    setupFormValidation() {
        // Validación en tiempo real para DNI
        this.elements.dniInput?.addEventListener('input', (e) => {
            const value = e.target.value.replace(/\D/g, ''); // Solo números
            e.target.value = value;
            
            if (value.length === 8) {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
            } else {
                e.target.classList.remove('is-valid');
                if (value.length > 0) e.target.classList.add('is-invalid');
            }
        });

        // Validación para teléfono
        const telefonoInput = document.getElementById('telefono');
        telefonoInput?.addEventListener('input', (e) => {
            const value = e.target.value.replace(/\D/g, ''); // Solo números
            e.target.value = value;
            
            if (value.length === 9) {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
            } else {
                e.target.classList.remove('is-valid');
                if (value.length > 0) e.target.classList.add('is-invalid');
            }
        });
    }

    async buscarDNI() {
        const dni = this.elements.dniInput?.value.trim();
        
        if (!this.validarDNI(dni)) {
            this.showMessage('warning', 'Ingrese un DNI válido de 8 dígitos');
            return;
        }

        this.toggleLoading(true);

        try {
            const res = await fetch(`${BASE_URL}personas/buscardni?q=${dni}`);
            const data = await res.json();

            this.procesarRespuestaDNI(data, dni);
            
        } catch (error) {
            console.error('Error buscando DNI:', error);
            this.showMessage('error', 'Error al consultar el DNI');
        } finally {
            this.toggleLoading(false);
        }
    }

    procesarRespuestaDNI(data, dni) {
        if (!data.success) {
            this.limpiarCampos();
            this.showMessage('info', data.message || 'No se encontró información');
            return;
        }

        if (data.registrado) {
            this.showMessage('info', 
                `El DNI ${dni} ya está registrado como: ${data.nombres} ${data.apepaterno} ${data.apematerno}`,
                'Persona ya registrada'
            );
            this.limpiarCampos();
        } else {
            this.autocompletarCampos(data);
            this.showMessage('success', 'Datos encontrados. Complete la información restante.');
        }
    }

    autocompletarCampos(data) {
        if (this.elements.apellidosInput) {
            this.elements.apellidosInput.value = `${data.apepaterno || ''} ${data.apematerno || ''}`.trim();
        }
        if (this.elements.nombresInput) {
            this.elements.nombresInput.value = data.nombres || '';
        }
        
        // Enfocar siguiente campo
        const telefonoInput = document.getElementById('telefono');
        telefonoInput?.focus();
    }

    limpiarCampos() {
        if (this.elements.apellidosInput) this.elements.apellidosInput.value = '';
        if (this.elements.nombresInput) this.elements.nombresInput.value = '';
    }

    toggleLoading(show) {
        if (this.elements.buscando) {
            this.elements.buscando.classList.toggle('d-none', !show);
        }
        if (this.elements.btnBuscar) {
            this.elements.btnBuscar.disabled = show;
        }
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Guardando...';

        try {
            const res = await fetch(form.action, { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                await this.handleSuccessfulSave(data);
            } else {
                this.handleValidationErrors(data);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showMessage('error', 'Error al procesar la solicitud');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Guardar';
        }
    }

    async handleSuccessfulSave(data) {
        const result = await Swal.fire({
            title: 'Persona guardada correctamente',
            text: '¿Deseas convertirla en lead?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Sí, convertir',
            cancelButtonText: 'No por ahora',
            reverseButtons: true
        });

        if (result.isConfirmed) {
            await this.abrirModalLead(data.idpersona);
        } else {
            window.location.href = `${BASE_URL}personas`;
        }
    }

    handleValidationErrors(data) {
        // Limpiar errores previos
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        if (data.errors) {
            let firstError = null;
            
            Object.entries(data.errors).forEach(([field, message]) => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = input.parentElement.querySelector('.invalid-feedback');
                    if (feedback) feedback.textContent = message;
                    if (!firstError) firstError = input;
                }
            });

            firstError?.focus();
            
            const mensajes = Object.values(data.errors).join('<br>');
            Swal.fire({ 
                title: 'Errores de validación', 
                html: mensajes, 
                icon: 'error' 
            });
        } else {
            this.showMessage('error', data.message);
        }
    }

    handleGlobalClick(e) {
        if (e.target.matches('.btn-convertir-lead') || e.target.closest('.btn-convertir-lead')) {
            const button = e.target.closest('.btn-convertir-lead');
            this.abrirModalLead(button.dataset.id);
        } else if (e.target.matches('.btn-eliminar') || e.target.closest('.btn-eliminar')) {
            const button = e.target.closest('.btn-eliminar');
            this.eliminarPersona(button.dataset.id);
        } else if (e.target.matches('.btn-editar') || e.target.closest('.btn-editar')) {
            const button = e.target.closest('.btn-editar');
            this.editarPersona(button.dataset.id);
        }
    }

    async abrirModalLead(idpersona) {
        try {
            const res = await fetch(`${BASE_URL}personas/modalCrear/${idpersona}`);
            if (!res.ok) throw new Error(`HTTP error ${res.status}`);
            
            const html = await res.text();
            this.elements.modalContainer.innerHTML = html;

            const modalEl = document.getElementById('leadModal');
            if (!modalEl) throw new Error('Modal no encontrado en el HTML');

            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            this.setupLeadModalHandlers(modalEl);

        } catch (error) {
            console.error('Error:', error);
            this.showMessage('error', 'No se pudo abrir el modal de Lead');
        }
    }

    setupLeadModalHandlers(modalEl) {
        const leadForm = modalEl.querySelector('#leadForm');
        if (!leadForm) return;

        leadForm.addEventListener('submit', async (e) => {
            await this.handleLeadSubmit(e, modalEl);
        });

        // Manejo de campos condicionales
        const origenSelect = modalEl.querySelector('#origenSelect');
        if (origenSelect) {
            const toggleFields = () => this.toggleConditionalFields(modalEl);
            toggleFields();
            origenSelect.addEventListener('change', toggleFields);
        }
    }

    toggleConditionalFields(modalEl) {
        const origenSelect = modalEl.querySelector('#origenSelect');
        const campaniaDiv = modalEl.querySelector('#campaniaDiv');
        const referidoDiv = modalEl.querySelector('#referidoDiv');

        if (!origenSelect) return;

        const selectedOption = origenSelect.options[origenSelect.selectedIndex];
        const tipo = selectedOption?.dataset.tipo || '';
        
        if (campaniaDiv) campaniaDiv.style.display = 'none';
        if (referidoDiv) referidoDiv.style.display = 'none';
        
        if (tipo === 'campaña' && campaniaDiv) {
            campaniaDiv.style.display = 'block';
        } else if (tipo === 'referido' && referidoDiv) {
            referidoDiv.style.display = 'block';
        }
    }

    async handleLeadSubmit(e, modalEl) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = form.querySelector('[type="submit"]');
        submitBtn.disabled = true;

        const formData = new FormData(form);
        
        // Corregir URL aquí
        const url = `${BASE_URL}personas/guardarLead`; // Agregada la 's'

        try {
            const res = await fetch(url, { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                await Swal.fire('¡Éxito!', data.message, 'success');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                modalInstance?.hide();
                window.location.href = `${BASE_URL}leads/index`;
            } else {
                this.handleLeadErrors(data, modalEl);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showMessage('error', 'Error al guardar el lead');
        } finally {
            submitBtn.disabled = false;
        }
    }

    handleLeadErrors(data, modalEl) {
        modalEl.querySelectorAll('.error-message').forEach(el => el.textContent = '');

        if (data.errors) {
            Object.entries(data.errors).forEach(([field, message]) => {
                const errorEl = modalEl.querySelector(`#${field}-error`);
                if (errorEl) errorEl.textContent = message;
            });
            this.showMessage('error', 'Revise los campos marcados');
        } else {
            this.showMessage('error', data.message);
        }
    }

    async buscarPersonas() {
        if (!this.elements.inputBuscar || !this.elements.tablaPersonas) return;

        const query = this.elements.inputBuscar.value.trim();
        
        this.elements.tablaPersonas.innerHTML = 
            '<tr><td colspan="6" class="text-center"><div class="spinner-border spinner-border-sm"></div> Buscando...</td></tr>';

        try {
            const res = await fetch(`${BASE_URL}personas/buscarAjax?q=${encodeURIComponent(query)}`);
            const data = await res.json();
            
            this.renderResultados(data);
            
        } catch (error) {
            console.error('Error:', error);
            this.elements.tablaPersonas.innerHTML = 
                '<tr><td colspan="6" class="text-center text-danger">Error al realizar la búsqueda</td></tr>';
        }
    }

    renderResultados(personas) {
        if (personas.length === 0) {
            this.elements.tablaPersonas.innerHTML = 
                '<tr><td colspan="6" class="text-center text-muted">No se encontraron resultados</td></tr>';
            return;
        }

        const colors = ['#8e44ad','#2980b9','#16a085','#e67e22','#c0392b'];
        
        this.elements.tablaPersonas.innerHTML = personas.map((persona, index) => {
            const color = colors[index % colors.length];
            const iniciales = persona.nombres.charAt(0) + (persona.apellidos?.charAt(0) || '');
            
            return `
                <tr>
                    <td>${persona.idpersona}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="person-avatar" style="background:${color};">
                                    ${iniciales.toUpperCase()}
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">${persona.nombres} ${persona.apellidos}</div>
                                <div class="small text-muted">${persona.direccion || ''}</div>
                            </div>
                        </div>
                    </td>
                    <td>${persona.dni}</td>
                    <td><a href="tel:${persona.telefono}">${persona.telefono}</a></td>
                    <td><a href="mailto:${persona.correo || ''}">${persona.correo || 'Sin correo'}</a></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-actions" role="group">
                            <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${persona.idpersona}">Editar</button>
                            <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${persona.idpersona}">Eliminar</button>
                            <button class="btn btn-sm btn-success btn-convertir-lead" data-id="${persona.idpersona}" title="Convertir en Lead">
                                <i class="bi bi-arrow-right-circle"></i> Lead
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async eliminarPersona(personaId) {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'No podrás revertir esta acción.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        });

        if (result.isConfirmed) {
            try {
                const res = await fetch(`${BASE_URL}personas/eliminar/${personaId}`, { 
                    method: 'POST' 
                });
                const data = await res.json();

                if (data.success) {
                    this.showMessage('success', data.message, 'Eliminado');
                    // Actualizar tabla si existe
                    if (this.elements.inputBuscar) {
                        this.buscarPersonas();
                    }
                } else {
                    this.showMessage('error', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showMessage('error', 'Error al eliminar la persona');
            }
        }
    }

    async editarPersona(personaId) {
        try {
            const res = await fetch(`${BASE_URL}personas/editar/${personaId}`);
            if (!res.ok) throw new Error('Error al cargar datos');
            
            const html = await res.text();
            this.elements.modalContainer.innerHTML = html;
            
            const modalEl = document.getElementById('editModal');
            if (!modalEl) throw new Error('Modal de edición no encontrado');

            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            this.setupEditModalHandlers(modalEl, modal);

        } catch (error) {
            console.error('Error:', error);
            this.showMessage('error', 'No se pudo abrir el modal de edición');
        }
    }

    setupEditModalHandlers(modalEl, modal) {
        const editForm = modalEl.querySelector('#form-editar-persona');
        if (!editForm) return;

        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = editForm.querySelector('[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Actualizando...';

            try {
                const formData = new FormData(editForm);
                const res = await fetch(editForm.action, { 
                    method: 'POST', 
                    body: formData 
                });
                const data = await res.json();

                if (data.success) {
                    this.showMessage('success', data.message, 'Actualizado');
                    modal.hide();
                    
                    // Actualizar tabla si existe
                    if (this.elements.inputBuscar) {
                        this.buscarPersonas();
                    }
                } else {
                    this.handleValidationErrors(data);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showMessage('error', 'Error al actualizar la persona');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // Funciones auxiliares
    activarBotonesAccion() {
        // Ya se maneja con delegación de eventos
        console.log('Eventos de botones configurados mediante delegación');
    }

    validarDNI(dni) {
        return dni && dni.length === 8 && /^\d+$/.test(dni);
    }

    showMessage(type, message, title = '') {
        const config = {
            icon: type,
            text: message,
            toast: true,
            position: 'bottom-end',
            timer: 4000,
            showConfirmButton: false,
            timerProgressBar: true
        };

        if (title) config.title = title;
        
        Swal.fire(config);
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.personaManager = new PersonaManager();
});

// Funciones globales para compatibilidad (si se necesitan)
function activarBotonesEliminar() {
    console.log('Función legacy - ahora manejada por PersonaManager');
}

function activarBotonesEditar() {
    console.log('Función legacy - ahora manejada por PersonaManager');
}

function activarBotonesConvertirLead() {
    console.log('Función legacy - ahora manejada por PersonaManager');
}