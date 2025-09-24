<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0 animate-fade-scale">
            <form id="leadForm" class="needs-validation" novalidate>
                <div class="modal-header bg-gradient-primary text-white border-0 rounded-top">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <div class="modal-icon bg-white bg-opacity-20 rounded-circle p-2 me-3">
                            <i class="bi bi-person-plus fs-5"></i>
                        </div>
                        Convertir a Lead
                    </h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover-lift" 
                            data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <!-- Mensajes de éxito o error -->
                    <div id="formMessage" class="alert d-none animate-slide-down" role="alert"></div>

                    <!-- Progress indicator modernizado -->
                    <div class="step-progress-container mb-4">
                        <div class="step-indicators">
                            <div class="step-indicator active" data-step="1">
                                <div class="step-number">1</div>
                                <div class="step-label">Información</div>
                            </div>
                            <div class="step-indicator" data-step="2">
                                <div class="step-number">2</div>
                                <div class="step-label">Configuración</div>
                            </div>
                            <div class="step-indicator" data-step="3">
                                <div class="step-number">3</div>
                                <div class="step-label">Confirmación</div>
                            </div>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-gradient-primary progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <!-- PASO 1: Información de la persona -->
                    <div class="step-content active" data-step="1">
                        <div class="card mb-4 border-0 shadow-sm hover-lift">
                            <div class="card-header bg-gradient-light border-0 rounded-top">
                                <h6 class="mb-0 d-flex align-items-center">
                                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-person-circle text-primary"></i>
                                    </div>
                                    <span class="fw-semibold">Información de la Persona</span>
                                    <span class="badge bg-success ms-auto">Verificado</span>
                                </h6>
                            </div>
                        <div class="card-body p-4">
                            <!-- Avatar y datos principales -->
                            <div class="d-flex align-items-center mb-4">
                                <div class="lead-avatar bg-gradient-primary text-white me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                    <?= strtoupper(substr($persona['nombres'] ?? 'N', 0, 1) . substr($persona['apellidos'] ?? 'A', 0, 1)) ?>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold text-dark">
                                        <?= esc($persona['nombres'] ?? 'Sin nombre') ?> <?= esc($persona['apellidos'] ?? '') ?>
                                    </h5>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-card-text me-1"></i>
                                        DNI: <?= esc($persona['dni'] ?? 'No registrado') ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Campos ocultos para formulario -->
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-light border-0" name="dni" id="dni"
                                               value="<?= esc($persona['dni'] ?? '') ?>" readonly>
                                        <label for="dni">DNI</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-light border-0" name="nombres" id="nombres"
                                               value="<?= esc($persona['nombres'] ?? '') ?>" readonly>
                                        <label for="nombres">Nombres</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-light border-0" name="apellidos" id="apellidos"
                                               value="<?= esc($persona['apellidos'] ?? '') ?>" readonly>
                                        <label for="apellidos">Apellidos</label>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($persona['correo']) || !empty($persona['telefono'])): ?>
                            <div class="row g-3 mt-2">
                                <?php if (!empty($persona['correo'])): ?>
                                <div class="col-md-6">
                                    <div class="input-group bg-light rounded">
                                        <span class="input-group-text bg-transparent border-0">
                                            <i class="bi bi-envelope text-primary"></i>
                                        </span>
                                        <input type="email" class="form-control bg-transparent border-0" 
                                               value="<?= esc($persona['correo']) ?>" readonly>
                                    </div>
                                    <small class="text-muted">Correo electrónico</small>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($persona['telefono'])): ?>
                                <div class="col-md-6">
                                    <div class="input-group bg-light rounded">
                                        <span class="input-group-text bg-transparent border-0">
                                            <i class="bi bi-phone text-success"></i>
                                        </span>
                                        <input type="tel" class="form-control bg-transparent border-0" 
                                               value="<?= esc($persona['telefono']) ?>" readonly>
                                    </div>
                                    <small class="text-muted">Teléfono</small>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Campos ocultos -->
                        <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">
                        <input type="hidden" name="idusuario" value="<?= session()->get('idusuario') ?? 1 ?>">
                    </div>
                    </div>

                    <!-- PASO 2: Configuración del Lead -->
                    <div class="step-content" data-step="2">
                        <div class="card border-0 shadow-sm hover-lift">
                            <div class="card-header bg-gradient-light border-0">
                                <h6 class="mb-0 d-flex align-items-center">
                                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-gear text-warning"></i>
                                    </div>
                                    <span class="fw-semibold">Configuración del Lead</span>
                                    <div class="ms-auto">
                                        <span class="badge bg-warning text-dark">Paso 2 de 3</span>
                                    </div>
                                </h6>
                            </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <!-- Origen -->
                                <div class="col-md-6">
                                    <label for="origenSelect" class="form-label fw-semibold d-flex align-items-center">
                                        <i class="bi bi-signpost me-2 text-primary"></i>
                                        Origen del Lead
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <select id="origenSelect" name="idorigen" class="form-select form-control-lg border-2 shadow-sm" 
                                            required data-validation="required">
                                        <option value="">🎯 Selecciona el origen...</option>
                                        <?php if (isset($origenes) && is_array($origenes)): ?>
                                            <?php foreach ($origenes as $origen): ?>
                                                <option value="<?= $origen['idorigen'] ?>" 
                                                        data-tipo="<?= strtolower(str_replace(' ', '_', $origen['nombre'])) ?>">
                                                    <?= esc($origen['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback fw-semibold" id="idorigen-error"></div>
                                    <small class="form-text text-muted">¿Cómo conoció nuestro servicio?</small>
                                </div>

                                <!-- Modalidad (Campo obligatorio) -->
                                <div class="col-md-6">
                                    <label for="modalidadesSelect" class="form-label fw-semibold d-flex align-items-center">
                                        <i class="bi bi-chat-dots me-2 text-success"></i>
                                        Modalidad de Contacto
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <select id="modalidadesSelect" name="idmodalidad" class="form-select form-control-lg border-2 shadow-sm" 
                                            required data-validation="required">
                                        <option value="">💬 Selecciona modalidad...</option>
                                        <?php if (isset($modalidades) && is_array($modalidades)): ?>
                                            <?php foreach ($modalidades as $modalidad): ?>
                                                <option value="<?= $modalidad['idmodalidad'] ?>">
                                                    <?= esc($modalidad['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback fw-semibold" id="idmodalidad-error"></div>
                                    <small class="form-text text-muted">¿Cómo prefiere ser contactado?</small>
                                </div>
                            </div>

                            <!-- Campos condicionales con animaciones -->
                            <div class="row g-4 mt-2">
                                <!-- Campaña (condicional) -->
                                <div id="campaniaDiv" class="col-md-6" style="display:none;">
                                    <div class="conditional-field animate-slide-down">
                                        <label for="campaniaSelect" class="form-label fw-semibold d-flex align-items-center">
                                            <i class="bi bi-megaphone me-2 text-info"></i>
                                            Campaña Específica
                                            <span class="text-danger ms-1">*</span>
                                        </label>
                                        <select id="campaniaSelect" name="idcampania" class="form-select form-control-lg border-2 shadow-sm">
                                            <option value="">📢 Selecciona campaña...</option>
                                            <?php if (isset($campanias) && is_array($campanias)): ?>
                                                <?php foreach ($campanias as $campana): ?>
                                                    <option value="<?= $campana['idcampania'] ?>"
                                                            <?= $campana['estado'] !== 'Activa' ? 'disabled' : '' ?>>
                                                        <?= esc($campana['nombre']) ?>
                                                        <?php if ($campana['estado'] !== 'Activa'): ?>
                                                            🔒 (Inactiva)
                                                        <?php else: ?>
                                                            ✅
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <div class="invalid-feedback fw-semibold" id="idcampania-error"></div>
                                        <small class="form-text text-primary">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Especifica qué campaña generó este lead
                                        </small>
                                    </div>
                                </div>

                                <!-- Referido por (condicional) -->
                                <div id="referidoDiv" class="col-md-6" style="display:none;">
                                    <div class="conditional-field animate-slide-down">
                                        <label for="referidoSelect" class="form-label fw-semibold d-flex align-items-center">
                                            <i class="bi bi-people me-2 text-warning"></i>
                                            Referido por
                                            <span class="text-danger ms-1">*</span>
                                        </label>
                                        <select id="referidoSelect" name="referido_por" class="form-select form-control-lg border-2 shadow-sm">
                                            <option value="">👥 Buscar persona...</option>
                                            <?php if (isset($personas) && is_array($personas)): ?>
                                                <?php foreach ($personas as $p): ?>
                                                    <?php if ($p['idpersona'] != $persona['idpersona']): ?>
                                                        <option value="<?= $p['idpersona'] ?>">
                                                            <?= esc($p['nombres'] . ' ' . $p['apellidos']) ?>
                                                            <?php if (!empty($p['dni'])): ?>
                                                                (DNI: <?= esc($p['dni']) ?>)
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <div class="invalid-feedback fw-semibold" id="referido_por-error"></div>
                                        <small class="form-text text-warning">
                                            <i class="bi bi-star me-1"></i>
                                            ¿Quién recomendó nuestros servicios?
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Medio de comunicación adicional -->
                            <div class="row g-4 mt-3">
                                <div class="col-12">
                                    <label for="medioInput" class="form-label fw-semibold d-flex align-items-center">
                                        <i class="bi bi-broadcast me-2 text-secondary"></i>
                                        Canal Específico
                                        <span class="badge bg-secondary ms-2">Opcional</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-link-45deg text-muted"></i>
                                        </span>
                                        <input type="text" id="medioInput" name="medio_comunicacion" 
                                               class="form-control border-start-0 shadow-sm" 
                                               placeholder="💻 Facebook, 📱 WhatsApp, 🌐 Página web, 📧 Email...">
                                    </div>
                                    <small class="form-text text-muted d-flex align-items-center mt-2">
                                        <i class="bi bi-lightbulb me-1"></i>
                                        Ayúdanos a entender qué canales funcionan mejor
                                    </small>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- PASO 3: Confirmación y Detalles Adicionales -->
                    <div class="step-content" data-step="3">
                        <div class="card border-0 shadow-sm hover-lift">
                            <div class="card-header bg-gradient-light border-0">
                                <h6 class="mb-0 d-flex align-items-center">
                                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-check-circle text-success"></i>
                                    </div>
                                    <span class="fw-semibold">Confirmación y Detalles</span>
                                    <div class="ms-auto">
                                        <span class="badge bg-success">Paso 3 de 3</span>
                                    </div>
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <!-- Resumen del Lead -->
                                <div class="lead-summary mb-4">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="bi bi-clipboard-check me-2"></i>
                                        Resumen del Lead
                                    </h6>
                                    <div class="summary-grid">
                                        <div class="summary-item">
                                            <span class="summary-label">Persona:</span>
                                            <span class="summary-value" id="summaryPersona">
                                                <?= esc($persona['nombres'] ?? '') ?> <?= esc($persona['apellidos'] ?? '') ?>
                                            </span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">DNI:</span>
                                            <span class="summary-value"><?= esc($persona['dni'] ?? '') ?></span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Origen:</span>
                                            <span class="summary-value" id="summaryOrigen">-</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Modalidad:</span>
                                            <span class="summary-value" id="summaryModalidad">-</span>
                                        </div>
                                        <div class="summary-item" id="summaryCampania" style="display: none;">
                                            <span class="summary-label">Campaña:</span>
                                            <span class="summary-value" id="summaryCampaniaValue">-</span>
                                        </div>
                                        <div class="summary-item" id="summaryReferido" style="display: none;">
                                            <span class="summary-label">Referido por:</span>
                                            <span class="summary-value" id="summaryReferidoValue">-</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Configuraciones adicionales -->
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="etapaSelect" class="form-label fw-semibold d-flex align-items-center">
                                            <i class="bi bi-list-task me-2 text-info"></i>
                                            Etapa Inicial
                                        </label>
                                        <select id="etapaSelect" name="idetapa" class="form-select form-control-lg border-2 shadow-sm">
                                            <option value="1" selected>📋 Prospecto</option>
                                            <option value="2">📞 Contactado</option>
                                            <option value="3">💬 En Negociación</option>
                                        </select>
                                        <small class="form-text text-muted">La etapa se puede cambiar después</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="prioridadSelect" class="form-label fw-semibold d-flex align-items-center">
                                            <i class="bi bi-flag me-2 text-warning"></i>
                                            Prioridad
                                        </label>
                                        <select id="prioridadSelect" name="prioridad" class="form-select form-control-lg border-2 shadow-sm">
                                            <option value="Media" selected>🟡 Media</option>
                                            <option value="Alta">🔴 Alta</option>
                                            <option value="Baja">🟢 Baja</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label for="observacionesInput" class="form-label fw-semibold d-flex align-items-center">
                                            <i class="bi bi-chat-quote me-2 text-secondary"></i>
                                            Observaciones Iniciales
                                            <span class="badge bg-secondary ms-2">Opcional</span>
                                        </label>
                                        <textarea id="observacionesInput" name="observaciones" 
                                                  class="form-control border-2 shadow-sm" rows="3"
                                                  placeholder="Añade cualquier información relevante sobre este lead..."></textarea>
                                        <small class="form-text text-muted">Notas que ayuden en el seguimiento futuro</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información sobre el proceso -->
                        <div class="alert alert-info border-0 shadow-sm bg-gradient-info mt-4">
                            <div class="d-flex align-items-start">
                                <div class="alert-icon bg-white bg-opacity-20 rounded-circle p-2 me-3">
                                    <i class="bi bi-info-circle fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-2">
                                        <strong> Proceso de Conversión</strong>
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary rounded-circle me-2">1</span>
                                                <small>Se creará en etapa inicial</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success rounded-circle me-2">2</span>
                                                <small>Asignación automática</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-warning rounded-circle me-2">3</span>
                                                <small>Seguimiento en Kanban</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 rounded-bottom p-4">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="bi bi-shield-check me-1"></i>
                            Información segura y protegida
                        </div>
                        <div class="step-navigation">
                            <button type="button" class="btn btn-outline-secondary btn-lg me-2" id="prevStepBtn" style="display: none;">
                                <i class="bi bi-arrow-left me-2"></i>
                                Anterior
                            </button>
                            <button type="button" class="btn btn-light btn-lg me-2 hover-lift" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i>
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary btn-lg me-2" id="nextStepBtn">
                                Siguiente
                                <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" class="btn btn-modern btn-lg shadow-sm" id="submitBtn" style="display: none;">
                                <i class="bi bi-rocket me-2"></i>
                                <span class="btn-text">Convertir a Lead</span>
                                <div class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Procesando...
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Estilos para el sistema de pasos */
.step-progress-container {
    margin-bottom: 2rem;
}

.step-indicators {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    position: relative;
}

.step-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.step-indicator.active {
    opacity: 1;
}

.step-indicator.completed {
    opacity: 1;
    color: #28a745;
}

.step-indicator:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 50%;
    width: 100%;
    height: 2px;
    background: #e9ecef;
    z-index: -1;
}

.step-indicator.completed:not(:last-child)::after {
    background: #28a745;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.step-indicator.active .step-number {
    background: #007bff;
    color: white;
}

.step-indicator.completed .step-number {
    background: #28a745;
    color: white;
}

.step-label {
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
}

.step-content {
    display: none;
    animation: fadeInUp 0.4s ease;
}

.step-content.active {
    display: block;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.summary-label {
    font-weight: 600;
    color: #6c757d;
}

.summary-value {
    font-weight: 500;
    color: #495057;
    text-align: right;
}

@media (max-width: 768px) {
    .step-indicators {
        font-size: 0.8rem;
    }
    
    .step-number {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }
    
    .summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
/**
 * SISTEMA MODAL MODERNIZADO DE CONVERSIÓN A LEAD
 * Incluye animaciones, validaciones mejoradas y UX optimizada
 */
document.addEventListener('DOMContentLoaded', function() {
    // ✨ ELEMENTOS DEL DOM
    const leadModal = document.getElementById('leadModal');
    const leadForm = document.getElementById('leadForm');
    const origenSelect = document.getElementById('origenSelect');
    const campaniaDiv = document.getElementById('campaniaDiv');
    const referidoDiv = document.getElementById('referidoDiv');
    const campaniaSelect = document.getElementById('campaniaSelect');
    const referidoSelect = document.getElementById('referidoSelect');
    const submitBtn = document.getElementById('submitBtn');
    const nextStepBtn = document.getElementById('nextStepBtn');
    const prevStepBtn = document.getElementById('prevStepBtn');
    const progressBar = leadModal.querySelector('.progress-bar');
    const stepIndicators = document.querySelectorAll('.step-indicator');
    const stepContents = document.querySelectorAll('.step-content');
    
    //  SISTEMA DE NAVEGACIÓN POR PASOS
    const StepNavigation = {
        currentStep: 1,
        totalSteps: 3,
        
        goToStep: function(stepNumber) {
            if (stepNumber < 1 || stepNumber > this.totalSteps) return;
            
            this.currentStep = stepNumber;
            this.updateUI();
            this.updateSummary();
        },
        
        nextStep: function() {
            if (this.currentStep < this.totalSteps) {
                if (this.validateCurrentStep()) {
                    this.currentStep++;
                    this.updateUI();
                    this.updateSummary();
                }
            }
        },
        
        prevStep: function() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.updateUI();
            }
        },
        
        updateUI: function() {
            // Actualizar progress bar
            const percentage = (this.currentStep / this.totalSteps) * 100;
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', percentage);
            
            // Actualizar indicadores de paso
            stepIndicators.forEach((indicator, index) => {
                const stepNum = index + 1;
                indicator.classList.remove('active', 'completed');
                
                if (stepNum < this.currentStep) {
                    indicator.classList.add('completed');
                } else if (stepNum === this.currentStep) {
                    indicator.classList.add('active');
                }
            });
            
            // Mostrar/ocultar contenido de pasos
            stepContents.forEach((content, index) => {
                content.classList.remove('active');
                if (index + 1 === this.currentStep) {
                    content.classList.add('active');
                }
            });
            
            // Actualizar botones de navegación
            prevStepBtn.style.display = this.currentStep > 1 ? 'inline-block' : 'none';
            nextStepBtn.style.display = this.currentStep < this.totalSteps ? 'inline-block' : 'none';
            submitBtn.style.display = this.currentStep === this.totalSteps ? 'inline-block' : 'none';
        },
        
        validateCurrentStep: function() {
            if (this.currentStep === 1) {
                // Paso 1: No necesita validación especial (datos ya verificados)
                return true;
            } else if (this.currentStep === 2) {
                // Paso 2: Validar origen y modalidad
                return ValidationSystem.validateStep2();
            }
            return true;
        },
        
        updateSummary: function() {
            if (this.currentStep === 3) {
                // Actualizar resumen en el paso 3
                const origenText = origenSelect.options[origenSelect.selectedIndex]?.text || '-';
                const modalidadText = document.getElementById('modalidadesSelect').options[document.getElementById('modalidadesSelect').selectedIndex]?.text || '-';
                
                document.getElementById('summaryOrigen').textContent = origenText;
                document.getElementById('summaryModalidad').textContent = modalidadText;
                
                // Mostrar campos condicionales en el resumen
                if (campaniaDiv.style.display !== 'none' && campaniaSelect.value) {
                    const campaniaText = campaniaSelect.options[campaniaSelect.selectedIndex]?.text || '-';
                    document.getElementById('summaryCampania').style.display = 'flex';
                    document.getElementById('summaryCampaniaValue').textContent = campaniaText;
                }
                
                if (referidoDiv.style.display !== 'none' && referidoSelect.value) {
                    const referidoText = referidoSelect.options[referidoSelect.selectedIndex]?.text || '-';
                    document.getElementById('summaryReferido').style.display = 'flex';
                    document.getElementById('summaryReferidoValue').textContent = referidoText;
                }
            }
        }
    };
    
    //  SISTEMA DE ANIMACIONES
    const AnimationSystem = {
        slideDown: (element) => {
            element.style.display = 'block';
            element.classList.add('animate-slide-down');
            element.style.opacity = '0';
            element.style.transform = 'translateY(-20px)';
            
            requestAnimationFrame(() => {
                element.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            });
        },
        
        slideUp: (element) => {
            element.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            element.style.opacity = '0';
            element.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                element.style.display = 'none';
                element.classList.remove('animate-slide-down');
            }, 300);
        },
        
        pulse: (element) => {
            element.classList.add('animate-pulse');
            setTimeout(() => {
                element.classList.remove('animate-pulse');
            }, 1000);
        }
    };
    
    //  SISTEMA DE VALIDACIÓN AVANZADO
    const ValidationSystem = {
        rules: {
            idorigen: {
                required: true,
                message: '🎯 Por favor selecciona el origen del lead'
            },
            idmodalidad: {
                required: true,
                message: '💬 Selecciona la modalidad de contacto preferida'
            },
            idcampania: {
                conditionalRequired: () => campaniaDiv.style.display !== 'none',
                message: '📢 Especifica qué campaña generó este lead'
            },
            referido_por: {
                conditionalRequired: () => referidoDiv.style.display !== 'none',
                message: '👥 Indica quién refirió a esta persona'
            }
        },
        
        validateField: function(fieldName, value) {
            const rule = this.rules[fieldName];
            if (!rule) return { valid: true };
            
            let isRequired = rule.required;
            if (rule.conditionalRequired) {
                isRequired = rule.conditionalRequired();
            }
            
            const valid = !isRequired || (value && value.trim() !== '');
            
            return {
                valid: valid,
                message: valid ? '' : rule.message
            };
        },
        
        validateStep2: function() {
            let isValid = true;
            
            // Validar origen
            const origenResult = this.validateField('idorigen', origenSelect.value);
            if (!origenResult.valid) {
                this.showFieldError('idorigen', origenResult.message);
                isValid = false;
            } else {
                this.clearFieldError('idorigen');
            }
            
            // Validar modalidad
            const modalidadSelect = document.getElementById('modalidadesSelect');
            const modalidadResult = this.validateField('idmodalidad', modalidadSelect.value);
            if (!modalidadResult.valid) {
                this.showFieldError('idmodalidad', modalidadResult.message);
                isValid = false;
            } else {
                this.clearFieldError('idmodalidad');
            }
            
            // Validar campos condicionales
            if (campaniaDiv.style.display !== 'none') {
                const campaniaResult = this.validateField('idcampania', campaniaSelect.value);
                if (!campaniaResult.valid) {
                    this.showFieldError('idcampania', campaniaResult.message);
                    isValid = false;
                } else {
                    this.clearFieldError('idcampania');
                }
            }
            
            if (referidoDiv.style.display !== 'none') {
                const referidoResult = this.validateField('referido_por', referidoSelect.value);
                if (!referidoResult.valid) {
                    this.showFieldError('referido_por', referidoResult.message);
                    isValid = false;
                } else {
                    this.clearFieldError('referido_por');
                }
            }
            
            return isValid;
        },

        validateAll: function() {
            let isValid = true;
            const errors = [];
            
            Object.keys(this.rules).forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (!field) return;
                
                const result = this.validateField(fieldName, field.value);
                
                if (!result.valid) {
                    isValid = false;
                    errors.push({
                        field: fieldName,
                        message: result.message
                    });
                    this.showFieldError(fieldName, result.message);
                } else {
                    this.clearFieldError(fieldName);
                }
            });
            
            return { valid: isValid, errors: errors };
        },
        
        showFieldError: function(fieldName, message) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            const errorDiv = document.getElementById(`${fieldName}-error`);
            
            if (field) {
                field.classList.add('is-invalid');
                AnimationSystem.pulse(field);
            }
            if (errorDiv) {
                errorDiv.textContent = message;
            }
        },
        
        clearFieldError: function(fieldName) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            const errorDiv = document.getElementById(`${fieldName}-error`);
            
            if (field) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
            if (errorDiv) {
                errorDiv.textContent = '';
            }
        },
        
        clearAllErrors: function() {
            Object.keys(this.rules).forEach(fieldName => {
                this.clearFieldError(fieldName);
            });
            
            // Limpiar también las clases is-valid
            setTimeout(() => {
                document.querySelectorAll('.is-valid').forEach(el => {
                    el.classList.remove('is-valid');
                });
            }, 2000);
        }
    };

    //  MANEJO INTELIGENTE DE CAMBIO DE ORIGEN
    origenSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const tipoOrigen = selectedOption.getAttribute('data-tipo');
        
        // Limpiar errores previos
        ValidationSystem.clearFieldError('idorigen');
        
        // Ocultar campos condicionales con animación
        if (campaniaDiv.style.display !== 'none') {
            AnimationSystem.slideUp(campaniaDiv);
        }
        if (referidoDiv.style.display !== 'none') {
            AnimationSystem.slideUp(referidoDiv);
        }
        
        // Limpiar valores y validaciones
        campaniaSelect.value = '';
        referidoSelect.value = '';
        ValidationSystem.clearFieldError('idcampania');
        ValidationSystem.clearFieldError('referido_por');
        
        // Actualizar progress
        if (this.value) {
            // No necesario actualizar aquí, se maneja en StepNavigation
        }
        
        // Mostrar campos específicos con animación
        setTimeout(() => {
            if (tipoOrigen === 'campaña' || tipoOrigen === 'campaña_digital') {
                AnimationSystem.slideDown(campaniaDiv);
                // Focus automático después de la animación
                setTimeout(() => {
                    campaniaSelect.focus();
                }, 500);
            } else if (tipoOrigen === 'referido') {
                AnimationSystem.slideDown(referidoDiv);
                setTimeout(() => {
                    referidoSelect.focus();
                }, 500);
            }
        }, 300);
        
        // Validación en tiempo real
        ValidationSystem.validateField('idorigen', this.value);
    });

    //  EVENT LISTENERS PARA NAVEGACIÓN POR PASOS
    nextStepBtn.addEventListener('click', function() {
        StepNavigation.nextStep();
    });
    
    prevStepBtn.addEventListener('click', function() {
        StepNavigation.prevStep();
    });
    
    // Navegación directa haciendo clic en los indicadores de paso
    stepIndicators.forEach((indicator, index) => {
        indicator.addEventListener('click', function() {
            const stepNumber = index + 1;
            if (stepNumber <= StepNavigation.currentStep || stepNumber === StepNavigation.currentStep - 1) {
                StepNavigation.goToStep(stepNumber);
            }
        });
    });
    
    // Inicializar el modal en el paso 1
    StepNavigation.updateUI();

    // 🚀 MANEJO AVANZADO DE ENVÍO DEL FORMULARIO
    leadForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validar paso actual antes de enviar
        if (!StepNavigation.validateCurrentStep()) {
            return;
        }
        
        // Cambiar estado del botón con animación
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');
        
        submitBtn.disabled = true;
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        
        // Limpiar mensajes previos
        clearMessages();
        
        // Validación completa
        const validation = ValidationSystem.validateAll();
        if (!validation.valid) {
            // Mostrar errores con animación
            showMessage('warning', '⚠️ Por favor completa todos los campos requeridos', validation.errors);
            resetSubmitButton();
            StepNavigation.goToStep(2); // Volver al paso anterior
            return;
        }
        
        // Obtener datos del formulario
        const formData = new FormData(this);
        
        try {
            // 📡 ENVÍO OPTIMIZADO CON FETCH API
            const response = await fetch('<?= base_url('leads/crear') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // ✅ ÉXITO - Animación de celebración
                showMessage('success', data.message || '🎉 ¡Lead creado exitosamente!');
                
                // Haptic feedback (si está disponible)
                if ('vibrate' in navigator) {
                    navigator.vibrate([100, 50, 100]);
                }
                
                // Animación de éxito en el botón
                submitBtn.classList.add('btn-success');
                btnLoading.innerHTML = '<i class="bi bi-check-circle me-2"></i>¡Creado!';
                
                // Progress completo
                progressBar.style.width = '100%';
                progressBar.classList.add('bg-success');
                
                // Cerrar modal después de mostrar éxito
                setTimeout(() => {
                    const modalInstance = bootstrap.Modal.getInstance(leadModal);
                    modalInstance.hide();
                    
                    // Recargar datos
                    if (typeof window.reloadPersonasTable === 'function') {
                        window.reloadPersonasTable();
                    } else {
                        location.reload();
                    }
                }, 2000);
                
            } else {
                // ERROR - Manejo mejorado
                throw new Error(data.message || 'Error desconocido al crear el lead');
            }
            
        } catch (error) {
            console.error('Error al crear lead:', error);
            
            // Mostrar error con contexto
            showMessage('error', `🚫 ${error.message}`, [{
                field: 'general',
                message: 'Verifica tu conexión e intenta nuevamente'
            }]);
            
            // Vibración de error
            if ('vibrate' in navigator) {
                navigator.vibrate(200);
            }
            
            resetSubmitButton();
            // Error ya mostrado, no cambiar paso
        }
    });
    
    // 🔄 FUNCIÓN PARA RESETEAR EL BOTÓN
    function resetSubmitButton() {
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');
        
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-success');
        btnText.classList.remove('d-none');
        btnLoading.classList.add('d-none');
        btnLoading.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
    }

    // 💬 SISTEMA DE MENSAJES MEJORADO
    function showMessage(type, message, errors = []) {
        const messageDiv = document.getElementById('formMessage');
        
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-triangle-fill', 
            warning: 'bi-exclamation-circle-fill',
            info: 'bi-info-circle-fill'
        };
        
        const colors = {
            success: 'alert-success',
            error: 'alert-danger',
            warning: 'alert-warning', 
            info: 'alert-info'
        };
        
        let errorsList = '';
        if (errors.length > 0) {
            errorsList = '<ul class="mt-2 mb-0">';
            errors.forEach(error => {
                errorsList += `<li><small>${error.message}</small></li>`;
            });
            errorsList += '</ul>';
        }
        
        messageDiv.className = `alert ${colors[type]} border-0 shadow-sm animate-slide-down`;
        messageDiv.innerHTML = `
            <div class="d-flex align-items-start">
                <i class="bi ${icons[type]} me-2 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>${message}</strong>
                    ${errorsList}
                </div>
            </div>
        `;
        
        messageDiv.classList.remove('d-none');
        
        // Auto-hide success messages
        if (type === 'success') {
            setTimeout(() => {
                messageDiv.classList.add('d-none');
            }, 5000);
        }
    }
    
    function clearMessages() {
        const messageDiv = document.getElementById('formMessage');
        messageDiv.classList.add('d-none');
        ValidationSystem.clearAllErrors();
    }

    // EVENTOS DE VALIDACIÓN EN TIEMPO REAL
    [origenSelect, document.getElementById('modalidadesSelect'), campaniaSelect, referidoSelect].forEach(field => {
        if (field) {
            field.addEventListener('change', function() {
                ValidationSystem.validateField(this.name, this.value);
            });
            
            field.addEventListener('blur', function() {
                ValidationSystem.validateField(this.name, this.value);
            });
        }
    });
    
    // +EVENTOS DEL MODAL
    leadModal.addEventListener('show.bs.modal', function() {
        // Reset progress
        StepNavigation.goToStep(1);
        progressBar.classList.remove('bg-success');
        
        // Focus en primer campo
        setTimeout(() => {
            origenSelect.focus();
        }, 500);
    });
    
    leadModal.addEventListener('hidden.bs.modal', function() {
        // Reset completo del formulario
        leadForm.reset();
        clearMessages();
        
        // Ocultar campos condicionales
        campaniaDiv.style.display = 'none';
        referidoDiv.style.display = 'none';
        
        // Reset botón
        resetSubmitButton();
        
        // Reset progress
        StepNavigation.goToStep(1);
        progressBar.classList.remove('bg-success');
        
        console.log('🔄 Modal de conversión reseteado');
    });
    
    // 📱 SOPORTE MÓVIL MEJORADO
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        // Ajustar modal para móviles
        leadModal.querySelector('.modal-dialog').classList.add('modal-fullscreen-sm-down');
        
        // Prevenir zoom en iOS
        document.querySelectorAll('input, select').forEach(input => {
            if (input.style.fontSize === '') {
                input.style.fontSize = '16px';
            }
        });
    }
    
    console.log('🚀 Sistema modal de conversión a lead inicializado');
});
</script>

<!-- ESTILOS ADICIONALES PARA EL MODAL -->
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
}

.modal-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-wrapper {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.conditional-field {
    opacity: 0;
    animation: slideInUp 0.4s ease-out forwards;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.btn-modern {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
    color: white;
}

.btn-modern:active {
    transform: translateY(0);
}

.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 10px;
    }
    
    .modal-body {
        padding: 1.5rem !important;
    }
    
    .btn-group-modern {
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-group-modern .btn {
        width: 100%;
    }
}
</style>