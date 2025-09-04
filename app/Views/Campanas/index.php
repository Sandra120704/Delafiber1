<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/campanas.css') ?>">

<div class="container-fluid mt-4">

    <!-- Encabezado con botón -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>📣 Campañas</h4>
        <button id="btnNuevaCampana" class="btn btn-primary">➕ Nueva Campaña</button>
    </div>

    <!-- Contenedor dinámico -->
    <div id="contenido-campanas">

        <!-- Lista de campañas -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5>📋 Lista de Campañas y Medios</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaCampanas" class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Campaña</th>
                                <th>Medio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($campanas as $c): ?>
                            <tr>
                                <td><?= $c['nombre'] ?></td>
                                <td><?= $c['medio'] ?></td>
                                <td>
                                    <button class="btn btn-sm <?= $c['estado'] == 'activo' ? 'btn-success' : 'btn-secondary' ?> btn-estado"
                                            data-id="<?= $c['idcampania'] ?>"
                                            data-estado="<?= $c['estado'] ?>">
                                        <?= ucfirst($c['estado']) ?>
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-edit" data-id="<?= $c['idcampania'] ?>">✏️</button>
                                    <button class="btn btn-danger btn-delete" data-id="<?= $c['idcampania'] ?>">🗑️</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<?= $footer ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    window.base_url = "<?= site_url('') ?>"; // ahora es global
</script>
<script src="<?= base_url('js/campana.js') ?>"></script>
