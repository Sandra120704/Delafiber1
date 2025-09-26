/**
 * JavaScript para Modal de Lead - Campos Condicionales
 * Este archivo debe ser lead-modal.js
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Lead Modal JS cargado');
    
    // Inicializar cuando se abre el modal
    const leadModal = document.getElementById('leadModal');
    if (leadModal) {
        leadModal.addEventListener('shown.bs.modal', function() {
            initializeLeadModal();
        });
        
        // Si el modal ya está visible, inicializar inmediatamente
        if (leadModal.classList.contains('show')) {
            initializeLeadModal();
        }
    }
});

function initializeLeadModal() {
    console.log('Inicializando modal de lead...');
    
    const elements = {
        origenSelect: document.getElementById('origenSelect'),
        campaniaDiv: document.getElementById('campaniaDiv'),
        campaniaSelect: document.getElementById('campaniaSelect'),
        referidoDiv: document.getElementById('referidoDiv'),
        buscarReferido: document.getElementById('buscarReferido'),
        btnBuscarReferido: document.getElementById('btnBuscarReferido'),
        resultadosReferido: document.getElementById('resultadosReferido'),
        listaResultados: document.getElementById('listaResultados'),
        crearNuevoReferido: document.getElementById('crearNuevoReferido'),
        nuevoReferidoForm: document.getElementById('nuevoReferidoForm'),
        referidoPorId: document.getElementById('referidoPorId'),
        crearReferidoInput: document.querySelector('input[name="crear_referido"]'),
        leadForm: document.getElementById('leadForm')
    };

    // Verificar que los elementos existan
    if (!elements.origenSelect) {
        console.error('No se encontró el select de origen');
        return;
    }

    console.log('Elementos encontrados, configurando eventos...');

    // Event Listeners
    elements.origenSelect.addEventListener('change', function() {
        console.log('Origen cambiado:', this.value, this.options[this.selectedIndex]?.dataset.tipo);
        handleOrigenChange();
    });

    if (elements.btnBuscarReferido) {
        elements.btnBuscarReferido.addEventListener('click', buscarPersonasReferido);
    }

    if (elements.buscarReferido) {
        elements.buscarReferido.addEventListener('keyup', debounce(buscarPersonasReferido, 500));
    }

    if (elements.crearNuevoReferido) {
        elements.crearNuevoReferido.addEventListener('change', toggleNuevoReferidoForm);
    }

    // Manejar envío del formulario
    if (elements.leadForm) {
        elements.leadForm.addEventListener('submit', handleFormSubmit);
    }

    // Inicializar estado
    handleOrigenChange();

    // Función para manejar cambio de origen
    function handleOrigenChange() {
        const selectedOption = elements.origenSelect.options[elements.origenSelect.selectedIndex];
        const tipo = selectedOption?.dataset.tipo || '';
        
        console.log('Manejando cambio de origen, tipo:', tipo);
        
        resetConditionalFields();
        
        if (tipo === 'campaña') {
            showCampaniaField();
        } else if (tipo === 'referido') {
            showReferidoField();
        } else {
            hideAllConditionalFields();
        }
        
        clearValidationErrors();
    }

    function resetConditionalFields() {
        console.log('Reseteando campos condicionales...');
        
        if (elements.campaniaSelect) elements.campaniaSelect.value = '';
        if (elements.referidoPorId) elements.referidoPorId.value = '';
        if (elements.buscarReferido) elements.buscarReferido.value = '';
        if (elements.crearNuevoReferido) elements.crearNuevoReferido.checked = false;
        
        // Limpiar formulario de nuevo referido
        const nuevoReferidoInputs = elements.nuevoReferidoForm?.querySelectorAll('input');
        nuevoReferidoInputs?.forEach(input => input.value = '');
        
        if (elements.crearReferidoInput) elements.crearReferidoInput.value = '0';
    }

    function showCampaniaField() {
        console.log('Mostrando campo de campaña...');
        
        if (elements.campaniaDiv) {
            elements.campaniaDiv.style.display = 'block';
            elements.campaniaSelect.required = true;
        }
        hideReferidoField();
    }

    function showReferidoField() {
        console.log('Mostrando campo de referido...');
        
        if (elements.referidoDiv) {
            elements.referidoDiv.style.display = 'block';
        }
        hideCampaniaField();
    }

    function hideCampaniaField() {
        if (elements.campaniaDiv) {
            elements.campaniaDiv.style.display = 'none';
            if (elements.campaniaSelect) elements.campaniaSelect.required = false;
        }
    }

    function hideReferidoField() {
        if (elements.referidoDiv) {
            elements.referidoDiv.style.display = 'none';
        }
        if (elements.resultadosReferido) {
            elements.resultadosReferido.style.display = 'none';
        }
        if (elements.nuevoReferidoForm) {
            elements.nuevoReferidoForm.style.display = 'none';
        }
    }

    function hideAllConditionalFields() {
        console.log('Ocultando todos los campos condicionales...');
        hideCampaniaField();
        hideReferidoField();
    }

    // Búsqueda de personas para referido
    async function buscarPersonasReferido() {
        const query = elements.buscarReferido?.value.trim();
        
        if (!query || query.length < 2) {
            if (elements.resultadosReferido) {
                elements.resultadosReferido.style.display = 'none';
            }
            return;
        }

        console.log('Buscando personas con query:', query);

        try {
            const response = await fetch(`${BASE_URL}personas/buscarAjax?q=${encodeURIComponent(query)}`);
            const personas = await response.json();
            
            console.log('Resultados encontrados:', personas.length);
            mostrarResultadosReferido(personas);
            
        } catch (error) {
            console.error('Error buscando personas:', error);
            mostrarMensajeError('Error al buscar personas');
        }
    }

    function mostrarResultadosReferido(personas) {
        if (!elements.listaResultados || !elements.resultadosReferido) return;
        
        elements.listaResultados.innerHTML = '';
        
        if (personas.length === 0) {
            elements.resultadosReferido.style.display = 'none';
            return;
        }

        personas.forEach(persona => {
            const item = document.createElement('a');
            item.href = '#';
            item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-start';
            item.innerHTML = `
                <div class="me-auto">
                    <div class="fw-bold">${persona.nombres} ${persona.apellidos}</div>
                    <small class="text-muted">DNI: ${persona.dni} | Tel: ${persona.telefono || 'N/A'}</small>
                </div>
                <span class="badge bg-primary rounded-pill">Seleccionar</span>
            `;
            
            item.addEventListener('click', (e) => {
                e.preventDefault();
                seleccionarReferido(persona);
            });
            
            elements.listaResultados.appendChild(item);
        });

        elements.resultadosReferido.style.display = 'block';
    }

    function seleccionarReferido(persona) {
        console.log('Seleccionando referido:', persona);
        
        if (elements.referidoPorId) {
            elements.referidoPorId.value = persona.idpersona;
        }
        
        if (elements.buscarReferido) {
            elements.buscarReferido.value = `${persona.nombres} ${persona.apellidos} (${persona.dni})`;
        }
        
        if (elements.resultadosReferido) {
            elements.resultadosReferido.style.display = 'none';
        }
        
        // Desmarcar crear nuevo referido
        if (elements.crearNuevoReferido) {
            elements.crearNuevoReferido.checked = false;
        }
        
        if (elements.nuevoReferidoForm) {
            elements.nuevoReferidoForm.style.display = 'none';
        }
        
        if (elements.crearReferidoInput) {
            elements.crearReferidoInput.value = '0';
        }

        mostrarMensajeExito('Persona seleccionada correctamente');
    }

    function toggleNuevoReferidoForm() {
        const mostrar = elements.crearNuevoReferido?.checked || false;
        
        console.log('Toggle nuevo referido form:', mostrar);
        
        if (elements.nuevoReferidoForm) {
            elements.nuevoReferidoForm.style.display = mostrar ? 'block' : 'none';
        }
        
        if (elements.crearReferidoInput) {
            elements.crearReferidoInput.value = mostrar ? '1' : '0';
        }
        
        if (mostrar) {
            // Limpiar selección anterior
            if (elements.referidoPorId) elements.referidoPorId.value = '';
            if (elements.buscarReferido) elements.buscarReferido.value = '';
            if (elements.resultadosReferido) elements.resultadosReferido.style.display = 'none';
            
            // Hacer campos requeridos
            const camposRequeridos = ['referidoDni', 'referidoNombres', 'referidoApellidos'];
            camposRequeridos.forEach(campoId => {
                const campo = document.getElementById(campoId);
                if (campo) campo.required = true;
            });
        } else {
            // Quitar requerimiento y limpiar
            const campos = elements.nuevoReferidoForm?.querySelectorAll('input');
            campos?.forEach(campo => {
                campo.required = false;
                campo.value = '';
            });
        }
    }

    // Manejar envío del formulario
    async function handleFormSubmit(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');
        
        // Mostrar estado de carga
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        submitBtn.disabled = true;

        try {
            const formData = new FormData(elements.leadForm);
            
            // Log para debug
            console.log('Enviando formulario con datos:');
            for (let [key, value] of formData.entries()) {
                console.log(key, ':', value);
            }
            
            const response = await fetch(`${BASE_URL}personas/guardarLead`, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            console.log('Respuesta del servidor:', data);
            
            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    confirmButtonText: 'Ver leads'
                });
                
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('leadModal'));
                if (modalInstance) modalInstance.hide();
                
                window.location.href = `${BASE_URL}leads/index`;
            } else {
                mostrarErroresValidacion(data.errors || { general: data.message });
            }
            
        } catch (error) {
            console.error('Error enviando formulario:', error);
            mostrarMensajeError('Error al procesar la solicitud');
        } finally {
            // Restaurar estado del botón
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            submitBtn.disabled = false;
        }
    }

    function mostrarErroresValidacion(errores) {
        clearValidationErrors();
        
        if (typeof errores === 'object') {
            Object.entries(errores).forEach(([campo, mensaje]) => {
                const input = document.querySelector(`[name="${campo}"]`) || document.getElementById(campo);
                const errorDiv = document.getElementById(`${campo}-error`);
                
                if (input) {
                    input.classList.add('is-invalid');
                }
                
                if (errorDiv) {
                    errorDiv.textContent = mensaje;
                } else {
                    console.warn(`No se encontró div de error para: ${campo}`);
                }
            });
            
            const mensajes = Object.values(errores).join('<br>');
            Swal.fire({
                icon: 'error',
                title: 'Errores de validación',
                html: mensajes
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errores
            });
        }
    }

    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }

    function mostrarMensajeExito(mensaje) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                text: mensaje,
                toast: true,
                position: 'bottom-end',
                timer: 3000,
                showConfirmButton: false
            });
        }
    }

    function mostrarMensajeError(mensaje) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                text: mensaje,
                toast: true,
                position: 'bottom-end',
                timer: 4000,
                showConfirmButton: false
            });
        }
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Función global para debug - puedes llamarla desde la consola
window.debugLeadModal = function() {
    console.log('=== DEBUG LEAD MODAL ===');
    console.log('Origen select:', document.getElementById('origenSelect'));
    console.log('Campaña div:', document.getElementById('campaniaDiv'));
    console.log('Referido div:', document.getElementById('referidoDiv'));
    console.log('BASE_URL:', typeof BASE_URL !== 'undefined' ? BASE_URL : 'NO DEFINIDA');
};