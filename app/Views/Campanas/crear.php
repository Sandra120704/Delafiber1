<div class="campana-form-container">
    <div class="campana-form-card card shadow-sm p-2">
        <div class="card-header py-1">
            <h6 class="mb-0">Registrar Nueva Campaña</h6>
        </div>
        <div class="card-body p-2">
            <form id="formCampana" method="post" action="<?= site_url('campanas/crear') ?>">
                <div class="mb-2">
                    <label class="form-label small">Nombre</label>
                    <input type="text" name="nombre" class="form-control form-control-sm" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Descripción</label>
                    <textarea name="descripcion" class="form-control form-control-sm" rows="2"></textarea>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label class="form-label small">Fecha Inicio</label>
                        <input type="date" name="fechainicio" class="form-control form-control-sm" required>
                    </div>
                    <div class="col">
                        <label class="form-label small">Fecha Fin</label>
                        <input type="date" name="fechafin" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label class="form-label small">Inversión</label>
                        <input type="number" step="0.01" name="inversion" class="form-control form-control-sm">
                    </div>
                    <div class="col">
                        <label class="form-label small">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-sm w-100">Guardar Campaña</button>
            </form>
        </div>
    </div>
</div>
