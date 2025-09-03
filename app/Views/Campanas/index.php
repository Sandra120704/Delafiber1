<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/persona.css') ?>">

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
                <h5>📋 Lista de Campañas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaCampanas" class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Inversión</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($campanas as $c): ?>
                            <tr>
                                <td><?= $c->idcampania ?></td>
                                <td><?= $c->nombre ?></td>
                                <td><?= $c->descripcion ?></td>
                                <td><?= $c->fechainicio ?></td>
                                <td><?= $c->fechafin ?></td>
                                <td><?= $c->inversion ?></td>
                                <td>
                                    <button class="btn btn-sm btn-estado <?= $c->estado === 'activo' ? 'btn-success' : 'btn-danger' ?>"
                                            data-id="<?= $c->idcampania ?>"
                                            data-estado="<?= $c->estado ?>">
                                        <?= ucfirst($c->estado) ?>
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-edit" data-id="<?= $c->idcampania ?>">✏️</button>
                                    <button class="btn btn-danger btn-delete" data-id="<?= $c->idcampania ?>">🗑️</button>
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
    const base_url = "<?= site_url('') ?>"; // termina con /
</script>
<script src="<?= base_url('js/campana.js') ?>"></script>
