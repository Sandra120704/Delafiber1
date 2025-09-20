
document.addEventListener('DOMContentLoaded', () => {
    console.log('Modal validation JS cargado');
    
    initModalValidation();
});

 // Inicializa las validaciones del modal principa
function initModalValidation() {
    const modalForm = document.getElementById('createCampaignForm');
    if (!modalForm) {
        console.warn('Formulario de modal no encontrado');
        return;
    }

    const fechaInicio = modalForm.querySelector('[name="fecha_inicio"]');
    const fechaFin = modalForm.querySelector('[name="fecha_fin"]');
    
    if (!fechaInicio || !fechaFin) {
        console.warn('Campos de fecha no encontrados en el modal');
        return;
    }

    // Configurar validaciones
    setupDateValidation(fechaInicio, fechaFin);
    setupFormValidation(modalForm);
}

/**
 * Configura la validación de fechas
 * @param {HTMLElement} fechaInicio - Campo fecha inicio
 * @param {HTMLElement} fechaFin - Campo fecha fin
 */
function setupDateValidation(fechaInicio, fechaFin) {
    function validarFechasModal() {
        if (fechaInicio.value && fechaFin.value) {
            const inicio = new Date(fechaInicio.value);
            const fin = new Date(fechaFin.value);
            
            if (fin <= inicio) {
                fechaFin.setCustomValidity('La fecha fin debe ser posterior a la fecha inicio');
                return false;
            } else {
                fechaFin.setCustomValidity('');
                return true;
            }
        }
        
        // Limpiar validación si no hay ambas fechas
        fechaFin.setCustomValidity('');
        return true;
    }
    
    // Event listeners
    fechaInicio.addEventListener('change', validarFechasModal);
    fechaFin.addEventListener('change', validarFechasModal);
    fechaInicio.addEventListener('blur', validarFechasModal);
    fechaFin.addEventListener('blur', validarFechasModal);
}

/**
 * Configura la validación general del formulario
 * @param {HTMLElement} form - Formulario del modal
 */
function setupFormValidation(form) {
    form.addEventListener('submit', (e) => {
        if (!validateModalForm(form)) {
            e.preventDefault();
            showValidationError('Por favor corrige los errores antes de continuar');
        }
    });
}

/**
 * Valida todo el formulario del modal
 * @param {HTMLElement} form - Formulario a validar
 * @returns {boolean} - True si es válido
 */
function validateModalForm(form) {
    const errors = [];
    
    // Validar campos requeridos
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            errors.push(`${getFieldLabel(field)} es obligatorio`);
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Validar fechas
    const fechaInicio = form.querySelector('[name="fecha_inicio"]');
    const fechaFin = form.querySelector('[name="fecha_fin"]');
    
    if (fechaInicio.value && fechaFin.value) {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        if (fin <= inicio) {
            errors.push('La fecha fin debe ser posterior a la fecha inicio');
        }
    }
    
    // Validar presupuesto
    const presupuesto = form.querySelector('[name="presupuesto"]');
    if (presupuesto.value && parseFloat(presupuesto.value) < 0) {
        errors.push('El presupuesto no puede ser negativo');
    }
    
    // Mostrar errores si los hay
    if (errors.length > 0) {
        showValidationErrors(errors);
        return false;
    }
    
    return true;
}

/**
 * Obtiene la etiqueta de un campo
 * @param {HTMLElement} field - Campo del formulario
 * @returns {string} - Texto de la etiqueta
 */
function getFieldLabel(field) {
    const label = document.querySelector(`label[for="${field.id}"]`) || 
                 field.closest('.mb-3')?.querySelector('label');
    return label ? label.textContent.replace('*', '').trim() : field.name;
}

/**
 * Muestra errores de validación
 * @param {Array} errors - Array de mensajes de error
 */
function showValidationErrors(errors) {
    const message = 'Errores encontrados:\n' + errors.map(err => `• ${err}`).join('\n');
    showValidationError(message);
}

/**
 * Muestra un error de validación
 * @param {string} message - Mensaje de error
 */
function showValidationError(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error de Validación',
            text: message,
            confirmButtonText: 'Entendido'
        });
    } else {
        alert(message);
    }
}

/**
 * Limpia las validaciones del formulario
 * @param {HTMLElement} form - Formulario a limpiar
 */
function clearFormValidation(form) {
    const invalidFields = form.querySelectorAll('.is-invalid');
    invalidFields.forEach(field => {
        field.classList.remove('is-invalid');
        field.setCustomValidity('');
    });
}

// Exportar funciones para uso externo si es necesario
window.ModalValidation = {
    validate: validateModalForm,
    clear: clearFormValidation,
    init: initModalValidation
};