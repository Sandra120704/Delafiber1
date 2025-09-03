<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/persona.css') ?>">

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>👤 Personas</h4>
        <button id="btnNuevaPersona" class="btn btn-primary">➕ Nueva Persona</button>
    </div>

    <div id="contenido-persona">
        <div class="card shadow-sm">
            <div class="card-header bg-light"><h5>📋 Lista de Personas</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaPersonas" class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th><th>Apellidos</th><th>Nombres</th>
                                <th>Teléfono</th><th>Email</th><th>Distrito</th>
                                <th>Estado</th><th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personas as $p): ?>
                            <tr>
                                <td><?= $p->idpersona ?></td>
                                <td><?= $p->apellidos ?></td>
                                <td><?= $p->nombres ?></td>
                                <td><?= $p->telprimario ?></td>
                                <td><?= $p->email ?></td>
                                <td><?= $p->distrito ?></td>
                                <td><span class="badge bg-success">Activo</span></td>
                                <td>
                                    <button class="btn btn-warning btn-sm btn-edit" data-id="<?= $p->idpersona ?>">✏️</button>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $p->idpersona ?>">🗑️</button>
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
    const base_url = "<?= site_url('') ?>";
</script>
<script src="<?= base_url('js/personas.js') ?>"></script>
