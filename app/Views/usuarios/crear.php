<div class="card mt-4">
    <div class="card-header d-flex justify-content-between">
        <h5>Nuevo Usuario</h5>
        <button id="btnVolverLista" class="btn btn-secondary btn-sm">🔙 Volver</button>
    </div>
    <div class="card-body">
        <form id="formUsuario" action="<?= base_url('usuarios/guardar') ?>" method="POST">
            <div class="mb-3">
                <label>Persona</label>
                <select name="idpersona" class="form-select" required>
                    <?php foreach($personas as $p): ?>
                        <option value="<?= $p->idpersona ?>"><?= $p->apellidos . ' ' . $p->nombres ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Rol</label>
                <select name="idrol" class="form-select" required>
                    <?php foreach($roles as $r): ?>
                        <option value="<?= $r['idrol'] ?>"><?= $r['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Crear Usuario</button>
        </form>
    </div>
</div>
