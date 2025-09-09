<?= $header ?>
<!-- <link rel="stylesheet" href="<?= base_url('css/usuarios.css') ?>"> -->

<div class="container mt-4">
    <h2>Usuarios</h2>

    <!-- Botón para crear nuevo usuario -->
    <button id="btnNuevoUsuario" class="btn btn-primary mb-3">Nuevo Usuario</button>

    <!-- Contenedor dinámico -->
    <div id="contenido-usuarios">
        <!-- Tabla de usuarios -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Persona</th>
                    <th>Username</th>
                    <th>Rol</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                <tr>
                    <td><?= $u['idusuario'] ?></td>
                    <td><?= $u['nombre_persona'] ?></td>
                    <td><?= $u['username'] ?></td>
                    <td><?= $u['nombre_rol'] ?></td>
                    <td><?= $u['activo'] ? 'Sí' : 'No' ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $u['idusuario'] ?>">Editar</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $u['idusuario'] ?>">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $footer ?>

<script>
    window.base_url = "<?= site_url('') ?>";
</script>
<script src="<?= base_url('js/usuarios.js') ?>"></script>
