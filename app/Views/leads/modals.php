<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded-xl shadow-2xl border-t-4 border-blue-500">
            <form id="leadForm">
                <div class="modal-header border-b border-gray-200 p-4 flex justify-between items-center">
                    <h5 class="modal-title text-xl font-bold text-gray-800">Convertir a Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-6">

                    <!-- Mensajes de éxito o error del servidor -->
                    <div id="formMessage" class="alert d-none rounded-md px-4 py-3 mb-4 text-sm" role="alert"></div>

                    <!-- Datos de la persona (solo lectura) -->
                    <div class="mb-4">
                        <label class="form-label text-sm font-medium">DNI</label>
                        <input type="text" class="form-control bg-gray-100" name="dni" value="<?= esc($persona['dni'] ?? '') ?>" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-sm font-medium">Nombres</label>
                        <input type="text" class="form-control bg-gray-100" name="nombres" value="<?= esc($persona['nombres'] ?? '') ?>" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-sm font-medium">Apellidos</label>
                        <input type="text" class="form-control bg-gray-100" name="apellidos" value="<?= esc($persona['apellidos'] ?? '') ?>" readonly>
                    </div>

                    <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">

                    <!-- Campo necesario para el Lead: Origen -->
                    <div class="mb-4">
                        <label for="origenSelect" class="form-label text-sm font-medium">Origen</label>
                        <select id="origenSelect" name="idorigen" class="form-select">
                            <option value="">Selecciona origen</option>
                            <?php foreach ($origenes as $origen): ?>
                                <option value="<?= $origen['idorigen'] ?>" data-tipo="<?= strtolower($origen['nombre']) ?>">
                                    <?= $origen['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-danger text-xs mt-1" id="idorigen-error"></div>
                    </div>

                    <!-- Campo necesario para el Lead: Modalidad -->
                    <div class="mb-4">
                        <label for="modalidadesSelect" class="form-label text-sm font-medium">Modalidad</label>
                        <select id="modalidadesSelect" name="idmodalidad" class="form-select">
                            <option value="">Selecciona modalidad</option>
                            <?php foreach ($modalidades as $modalidad): ?>
                                <option value="<?= $modalidad['idmodalidad'] ?>">
                                    <?= $modalidad['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-danger text-xs mt-1" id="idmodalidad-error"></div>
                    </div>

                    <!-- Campos condicionales (inicialmente ocultos) -->
                    <div id="campaniaDiv" class="mb-4" style="display:none;">
                        <label for="campaniaSelect" class="form-label text-sm font-medium">Campaña</label>
                        <select id="campaniaSelect" name="idcampania" class="form-select">
                            <option value="">Selecciona campaña</option>
                            <?php foreach ($campanias as $campana): ?>
                                <option value="<?= $campana['idcampania'] ?>"><?= $campana['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-danger text-xs mt-1" id="idcampania-error"></div>
                    </div>

                    <div id="referidoDiv" class="mb-4" style="display:none;">
                        <label for="referidoSelect" class="form-label text-sm font-medium">Referido por</label>
                        <select id="referidoSelect" name="referido_por" class="form-select">
                            <option value="">Selecciona persona</option>
                            <?php foreach ($personas as $p): ?>
                                <option value="<?= $p['idpersona'] ?>">
                                    <?= esc($p['nombres']) . ' ' . esc($p['apellidos']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-danger text-xs mt-1" id="referido_por-error"></div>
                    </div>

                </div>
                <div class="modal-footer border-t border-gray-200 p-4 flex justify-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?= base_url('js/leadsJS/leadsForm.js') ?>"></script>