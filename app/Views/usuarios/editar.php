<div class="container mt-4">
    <h2>Editar Usuario</h2>
    <form id="formUsuario" action="<?= base_url('usuarios/actualizar') ?>" method="POST">
        <input type="hidden" name="idusuario" value="<?= $usuario['idusuario'] ?>">

        <!-- Persona -->
        <div class="mb-3">
            <label>Persona</label>
            <select name="idpersona" class="form-select" required>
                <?php foreach($personas as $p): ?>
                    <option value="<?= $p['idpersona'] ?>" <?= $usuario['idpersona'] == $p['idpersona'] ? 'selected' : '' ?>>
                        <?= $p['apellidos'] . ' ' . $p['nombres'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Username -->
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?= $usuario['username'] ?>" required>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label>Password (dejar vacío si no desea cambiar)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <!-- Rol -->
        <div class="mb-3">
            <label>Rol</label>
            <select name="idrol" class="form-select" required>
                <?php foreach($roles as $r): ?>
                    <option value="<?= $r['idrol'] ?>" <?= $usuario['idrol'] == $r['idrol'] ? 'selected' : '' ?>>
                        <?= $r['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn btn-success">Actualizar Usuario</button>
    </form>
</div>
