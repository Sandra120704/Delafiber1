<div class="card form-card mt-4">
    <div class="card-header d-flex justify-content-between">
        <h5><?= isset($persona) ? 'Editar Persona' : 'Nueva Persona' ?></h5>
        <button id="btnVolverLista" class="btn btn-secondary btn-sm">🔙 Volver</button>
    </div>
    <div class="card-body">
        <form id="formPersona" method="post" action="<?= isset($persona) ? base_url('persona/guardar') : base_url('persona/guardar') ?>"
              data-iddepartamento="<?= $persona->iddepartamento ?? '' ?>"
              data-idprovincia="<?= $persona->idprovincia ?? '' ?>"
              data-iddistrito="<?= $persona->iddistrito ?? '' ?>">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" value="<?= $persona->apellidos ?? '' ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Nombres</label>
                    <input type="text" name="nombres" class="form-control" value="<?= $persona->nombres ?? '' ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Teléfono</label>
                    <input type="text" name="telprimario" class="form-control" value="<?= $persona->telprimario ?? '' ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= $persona->email ?? '' ?>">
                </div>
                <div class="col-md-4 mb-3 mt-4">
                    <label>Departamento</label>
                    <select id="departamento" name="iddepartamento" class="form-select">
                        <option value="">Seleccione...</option>
                        <?php foreach ($departamentos as $d): ?>
                            <option value="<?= $d['iddepartamento'] ?>"><?= $d['departamento'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mt-4">
                    <label>Provincia</label>
                    <select id="provincia" name="idprovincia" class="form-select"></select>
                </div>
                <div class="col-md-4 mb-3 mt-4">
                    <label>Distrito</label>
                    <select id="distrito" name="iddistrito" class="form-select"></select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success">💾 <?= isset($persona) ? 'Guardar Cambios' : 'Registrar' ?></button>
            </div>
        </form>
    </div>
</div>
