<?= $header ?>

<div class="container mt-2">
    <div class="my-2">
        <h3><?= isset($campana) ? 'Editar Campaña' : 'Registrar Campaña' ?></h3>
        <a href="<?= base_url('campanas');?>" class="btn btn-sm btn-secondary">Lista de Campañas</a>
    </div>

    <form action="<?= base_url('campana/guardar') ?>" id="form-campana" method="POST" autocomplete="off">
        <input type="hidden" name="idcampania" value="<?= $campana['idcampania'] ?? '' ?>">
        <div class="card">
            <div class="card-body">

                <div class="mb-2">
                    <label for="nombre">Nombre de la Campaña</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" 
                           value="<?= $campana['nombre'] ?? '' ?>" required>
                </div>

                <div class="mb-2">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" name="descripcion" id="descripcion"><?= $campana['descripcion'] ?? '' ?></textarea>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" 
                               value="<?= $campana['fecha_inicio'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_fin">Fecha Fin</label>
                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" 
                               value="<?= $campana['fecha_fin'] ?? '' ?>" required>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label for="presupuesto">Presupuesto</label>
                        <input type="number" step="0.01" class="form-control" name="presupuesto" id="presupuesto" 
                               value="<?= $campana['presupuesto'] ?? '' ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="Activo" <?= (isset($campana['estado']) && $campana['estado']=='Activo')?'selected':'' ?>>Activo</option>
                            <option value="Inactivo" <?= (isset($campana['estado']) && $campana['estado']=='Inactivo')?'selected':'' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="mb-2">
                    <label>Medios de Difusión</label>
                   <?php foreach($medios as $m): ?>
                    <div class="form-control">
                        <input class="form-check-label" type="radio" name="medio" value="<?= $m['idmedio'] ?>"
                            <?= (isset($difusiones[0]) && $difusiones[0]==$m['idmedio']) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= $m['nombre'] ?></label>
                    </div>
                <?php endforeach; ?>
                </div>

            </div>
            <div class="card-footer text-end">
                <button class="btn btn-sm btn-outline-secondary" type="reset">Cancelar</button>
                <button class="btn btn-sm btn-primary" type="submit"><?= isset($campana) ? 'Guardar Cambios' : 'Registrar Campaña' ?></button>
            </div>
        </div>
    </form>
</div>

<?= $footer ?>
