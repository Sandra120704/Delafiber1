<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="leadForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus"></i> Convertir a Lead
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Mensajes de éxito o error -->
                    <div id="formMessage" class="alert d-none" role="alert"></div>

                    <!-- Información de la persona -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-person-circle"></i> Información de la Persona
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">DNI</label>
                                    <input type="text" class="form-control bg-light" name="dni" 
                                           value="<?= esc($persona['dni'] ?? '') ?>" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nombres</label>
                                    <input type="text" class="form-control bg-light" name="nombres" 
                                           value="<?= esc($persona['nombres'] ?? '') ?>" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Apellidos</label>
                                    <input type="text" class="form-control bg-light" name="apellidos" 
                                           value="<?= esc($persona['apellidos'] ?? '') ?>" readonly>
                                </div>
                            </div>
                            
                            <?php if (!empty($persona['correo']) || !empty($persona['telefono'])): ?>
                            <div class="row">
                                <?php if (!empty($persona['correo'])): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Correo</label>
                                    <input type="email" class="form-control bg-light" 
                                           value="<?= esc($persona['correo']) ?>" readonly>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($persona['telefono'])): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control bg-light" 
                                           value="<?= esc($persona['telefono']) ?>" readonly>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Campos ocultos -->
                    <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">
                    <input type="hidden" name="idusuario" value="<?= session()->get('idusuario') ?? 1 ?>">

                    <!-- Configuración del Lead -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-gear"></i> Configuración del Lead
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Origen -->
                                <div class="col-md-6 mb-3">
                                    <label for="origenSelect" class="form-label">
                                        Origen <span class="text-danger">*</span>
                                    </label>
                                    <select id="origenSelect" name="idorigen" class="form-select" required>
                                        <option value="">Selecciona origen...</option>
                                        <?php if (isset($origenes) && is_array($origenes)): ?>
                                            <?php foreach ($origenes as $origen): ?>
                                                <option value="<?= $origen['idorigen'] ?>" 
                                                        data-tipo="<?= strtolower(str_replace(' ', '_', $origen['nombre'])) ?>">
                                                    <?= esc($origen['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback" id="idorigen-error"></div>
                                </div>

                                <!-- Modalidad (Campo obligatorio) -->
                                <div class="col-md-6 mb-3">
                                    <label for="modalidadesSelect" class="form-label">
                                        Modalidad de Contacto <span class="text-danger">*</span>
                                    </label>
                                    <select id="modalidadesSelect" name="idmodalidad" class="form-select" required>
                                        <option value="">Selecciona modalidad...</option>
                                        <?php if (isset($modalidades) && is_array($modalidades)): ?>
                                            <?php foreach ($modalidades as $modalidad): ?>
                                                <option value="<?= $modalidad['idmodalidad'] ?>">
                                                    <?= esc($modalidad['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback" id="idmodalidad-error"></div>
                                </div>
                            </div>

                            <!-- Campos condicionales -->
                            <div class="row">
                                <!-- Campaña -->
                                <div id="campaniaDiv" class="col-md-6 mb-3" style="display:none;">
                                    <label for="campaniaSelect" class="form-label">
                                        Campaña <span class="text-danger">*</span>
                                    </label>
                                    <select id="campaniaSelect" name="idcampania" class="form-select">
                                        <option value="">Selecciona campaña...</option>
                                        <?php if (isset($campanias) && is_array($campanias)): ?>
                                            <?php foreach ($campanias as $campana): ?>
                                                <option value="<?= $campana['idcampania'] ?>">
                                                    <?= esc($campana['nombre']) ?>
                                                    <?php if ($campana['estado'] !== 'Activa'): ?>
                                                        (Inactiva)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback" id="idcampania-error"></div>
                                    <small class="form-text text-muted">
                                        Requerido cuando el origen es "Campaña"
                                    </small>
                                </div>

                                <!-- Referido por (condicional) -->
                                <div id="referidoDiv" class="col-md-6 mb-3" style="display:none;">
                                    <label for="referidoSelect" class="form-label">
                                        Referido por <span class="text-danger">*</span>
                                    </label>
                                    <select id="referidoSelect" name="referido_por" class="form-select">
                                        <option value="">Buscar persona...</option>
                                        <?php if (isset($personas) && is_array($personas)): ?>
                                            <?php foreach ($personas as $p): ?>
                                                <?php if ($p['idpersona'] != $persona['idpersona']): // No incluir la misma persona ?>
                                                    <option value="<?= $p['idpersona'] ?>">
                                                        <?= esc($p['nombres'] . ' ' . $p['apellidos']) ?>
                                                        <?php if (!empty($p['dni'])): ?>
                                                            (<?= esc($p['dni']) ?>)
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback" id="referido_por-error"></div>
                                    <small class="form-text text-muted">
                                        Requerido cuando el origen es "Referido"
                                    </small>
                                </div>
                            </div>

                            <!-- Medio de comunicación adicional -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="medioInput" class="form-label">
                                        Medio de Comunicación (opcional)
                                    </label>
                                    <input type="text" id="medioInput" name="medio_comunicacion" 
                                           class="form-control" placeholder="Ej: Facebook, WhatsApp, Página web...">
                                    <small class="form-text text-muted">
                                        Especifica el canal específico por donde llegó el lead
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Información:</strong>
                            <ul class="mb-0 mt-2">
                                <li>El lead se creará en la primera etapa del pipeline</li>
                                <li>Se asignará automáticamente al usuario actual</li>
                                <li>Podrás hacer seguimiento desde el módulo de Leads</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-check-circle"></i> Crear Lead
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const leadModal = document.getElementById('leadModal');
    const leadForm = document.getElementById('leadForm');
    const origenSelect = document.getElementById('origenSelect');
    const campaniaDiv = document.getElementById('campaniaDiv');
    const referidoDiv = document.getElementById('referidoDiv');
    const campaniaSelect = document.getElementById('campaniaSelect');
    const referidoSelect = document.getElementById('referidoSelect');
    const submitBtn = document.getElementById('submitBtn');

    // Manejar cambio en el origen
    origenSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const tipoOrigen = selectedOption.getAttribute('data-tipo');
        
        // Ocultar todos los campos condicionales
        campaniaDiv.style.display = 'none';
        referidoDiv.style.display = 'none';
        
        // Limpiar valores
        campaniaSelect.value = '';
        referidoSelect.value = '';
        
        // Remover required de campos condicionales
        campaniaSelect.removeAttribute('required');
        referidoSelect.removeAttribute('required');
        
        // Mostrar campos según el tipo de origen
        if (tipoOrigen === 'campaña' || tipoOrigen === 'campaña_digital') {
            campaniaDiv.style.display = 'block';
            campaniaSelect.setAttribute('required', 'required');
        } else if (tipoOrigen === 'referido') {
            referidoDiv.style.display = 'block';
            referidoSelect.setAttribute('required', 'required');
        }
    });

    // Manejar envío del formulario
    leadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Deshabilitar botón de envío
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creando...';
        
        // Limpiar mensajes previos
        clearMessages();
        
        // Obtener datos del formulario
        const formData = new FormData(this);
        
        // Validar campos requeridos
        if (!validateForm()) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Crear Lead';
            return;
        }
        
        // Enviar petición AJAX
        fetch('<?= base_url('leads/crear') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('success', data.message || 'Lead creado exitosamente');
                setTimeout(() => {
                    bootstrap.Modal.getInstance(leadModal).hide();
                    if (typeof window.reloadPersonasTable === 'function') {
                        window.reloadPersonasTable();
                    } else {
                        location.reload();
                    }
                }, 1500);
            } else {
                showMessage('error', data.message || 'Error al crear el lead');
                if (data.errors) {
                    showFieldErrors(data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('error', 'Error de conexión. Por favor, intente nuevamente.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Crear Lead';
        });
    });

    function validateForm() {
        let isValid = true;
        
        // Validar origen
        if (!origenSelect.value) {
            showFieldError('idorigen', 'Debe seleccionar un origen');
            isValid = false;
        }
        
        // Validar modalidad
        const modalidadSelect = document.getElementById('modalidadesSelect');
        if (!modalidadSelect.value) {
            showFieldError('idmodalidad', 'Debe seleccionar una modalidad');
            isValid = false;
        }
        
        // Validar campos condicionales
        if (campaniaDiv.style.display !== 'none' && !campaniaSelect.value) {
            showFieldError('idcampania', 'Debe seleccionar una campaña');
            isValid = false;
        }
        
        if (referidoDiv.style.display !== 'none' && !referidoSelect.value) {
            showFieldError('referido_por', 'Debe seleccionar quién refirió');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showMessage(type, message) {
        const messageDiv = document.getElementById('formMessage');
        messageDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
        messageDiv.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}`;
        messageDiv.classList.remove('d-none');
    }
    
    function clearMessages() {
        const messageDiv = document.getElementById('formMessage');
        messageDiv.classList.add('d-none');
        
        // Limpiar errores de campos
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-control, .form-select').forEach(el => {
            el.classList.remove('is-invalid');
        });
    }
    
    function showFieldError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        const errorDiv = document.getElementById(`${fieldName}-error`);
        
        if (field) {
            field.classList.add('is-invalid');
        }
        if (errorDiv) {
            errorDiv.textContent = message;
        }
    }
    
    function showFieldErrors(errors) {
        Object.entries(errors).forEach(([field, message]) => {
            showFieldError(field, message);
        });
    }

    // Limpiar formulario al cerrar modal
    leadModal.addEventListener('hidden.bs.modal', function() {
        leadForm.reset();
        clearMessages();
        campaniaDiv.style.display = 'none';
        referidoDiv.style.display = 'none';
    });
});
</script>