
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="leadForm" novalidate>
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i> Convertir a Lead
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Información de la persona -->
                    <div class="card bg-light mb-4">
                        <div class="card-body p-3">
                            <h6 class="text-muted mb-2">Datos de la Persona</h6>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <small class="text-muted">DNI</small>
                                    <p class="mb-0 fw-bold"><?= esc($persona['dni'] ?? '') ?></p>
                                </div>
                                <div class="col-md-5">
                                    <small class="text-muted">Nombres</small>
                                    <p class="mb-0 fw-bold"><?= esc($persona['nombres'] ?? '') ?></p>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Apellidos</small>
                                    <p class="mb-0 fw-bold"><?= esc($persona['apellidos'] ?? '') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Origen -->
                    <div class="mb-3">
                        <label for="origenSelect" class="form-label">Origen *</label>
                        <select name="idorigen" id="origenSelect" class="form-select" required>
                            <option value="">¿Cómo nos conoció?</option>
                            <?php foreach ($origenes as $origen): ?>
                                <option value="<?= $origen['idorigen'] ?>" 
                                        data-tipo="<?= esc($origen['tipo'] ?? '') ?>">
                                    <?= esc($origen['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback" id="idorigen-error"></div>
                    </div>

                    <!-- Modalidad -->
                    <div class="mb-3">
                        <label for="modalidadesSelect" class="form-label">Modalidad de Interés *</label>
                        <select name="idmodalidad" id="modalidadesSelect" class="form-select" required>
                            <option value="">Seleccione modalidad...</option>
                            <?php foreach ($modalidades as $modalidad): ?>
                                <option value="<?= $modalidad['idmodalidad'] ?>">
                                    <?= esc($modalidad['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback" id="idmodalidad-error"></div>
                    </div>

                    <!-- CAMPO CONDICIONAL: Campaña -->
                    <div id="campaniaDiv" class="mb-3" style="display: none;">
                        <div class="alert alert-info">
                            <i class="bi bi-megaphone me-2"></i>
                            <strong>Campaña de Marketing</strong>
                            <p class="mb-0 small">Esta persona llegó a través de una campaña publicitaria</p>
                        </div>
                        <label for="campaniaSelect" class="form-label">Campaña *</label>
                        <select name="idcampania" id="campaniaSelect" class="form-select">
                            <option value="">Seleccione la campaña...</option>
                            <?php foreach ($campanias as $campania): ?>
                                <option value="<?= $campania['idcampania'] ?>">
                                    <?= esc($campania['nombre']) ?> 
                                    <?php if (isset($campania['fecha_inicio'])): ?>
                                        - (<?= date('M Y', strtotime($campania['fecha_inicio'])) ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback" id="idcampania-error"></div>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> 
                            Esta información ayuda a medir la efectividad de nuestras campañas
                        </div>
                    </div>

                    <!-- CAMPO CONDICIONAL: Referido -->
                    <div id="referidoDiv" class="mb-3" style="display: none;">
                        <div class="alert alert-success">
                            <i class="bi bi-people me-2"></i>
                            <strong>Referido por Cliente</strong>
                            <p class="mb-0 small">Esta persona fue referida por alguien que ya conocemos</p>
                        </div>
                        
                        <!-- Buscar persona existente o crear nueva -->
                        <div class="mb-3">
                            <label for="buscarReferido" class="form-label">Buscar quien lo refirió</label>
                            <div class="input-group">
                                <input type="text" id="buscarReferido" class="form-control" 
                                       placeholder="Escriba nombre, apellido o DNI..." autocomplete="off">
                                <button type="button" id="btnBuscarReferido" class="btn btn-outline-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <div class="form-text">Busque en la base de datos existente</div>
                        </div>

                        <!-- Resultados de búsqueda -->
                        <div id="resultadosReferido" class="mb-3" style="display: none;">
                            <label class="form-label">Seleccionar de resultados encontrados:</label>
                            <div class="list-group" id="listaResultados"></div>
                        </div>

                        <!-- Opción para crear nuevo referido -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="crearNuevoReferido">
                                <label class="form-check-label" for="crearNuevoReferido">
                                    La persona que lo refirió no está registrada (crear nuevo)
                                </label>
                            </div>
                        </div>

                        <!-- Formulario para nuevo referido -->
                        <div id="nuevoReferidoForm" class="card border-secondary" style="display: none;">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-person-plus me-2"></i>
                                    Registrar nueva persona que lo refirió
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="referidoDni" class="form-label">DNI *</label>
                                        <input type="text" name="referido_dni" id="referidoDni" 
                                               class="form-control" maxlength="8" pattern="[0-9]{8}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="referidoNombres" class="form-label">Nombres *</label>
                                        <input type="text" name="referido_nombres" id="referidoNombres" 
                                               class="form-control" maxlength="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="referidoApellidos" class="form-label">Apellidos *</label>
                                        <input type="text" name="referido_apellidos" id="referidoApellidos" 
                                               class="form-control" maxlength="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="referidoTelefono" class="form-label">Teléfono</label>
                                        <input type="tel" name="referido_telefono" id="referidoTelefono" 
                                               class="form-control" maxlength="9" pattern="[0-9]{9}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="referidoCorreo" class="form-label">Correo</label>
                                        <input type="email" name="referido_correo" id="referidoCorreo" 
                                               class="form-control" maxlength="150">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo oculto para el ID del referido seleccionado -->
                        <input type="hidden" name="referido_por" id="referidoPorId">
                        <div class="invalid-feedback" id="referido_por-error"></div>
                    </div>

                    <!-- Observaciones adicionales -->
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">
                            <i class="bi bi-chat-left-text me-1"></i> Observaciones
                        </label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="3"
                                  placeholder="Información adicional sobre el prospecto o la conversación..."></textarea>
                        <div class="form-text">Este campo se guardará en el historial del lead</div>
                    </div>

                    <!-- Campos ocultos -->
                    <input type="hidden" name="idpersona" value="<?= esc($persona['idpersona']) ?>">
                    <input type="hidden" name="crear_referido" value="0">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="btn-text">
                            <i class="bi bi-check-lg me-1"></i> Crear Lead
                        </span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Procesando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const BASE_URL = "<?= rtrim(base_url(), '/') ?>/";
</script>
<script src="<?= base_url('js/lead-modal.js') ?>"></script>