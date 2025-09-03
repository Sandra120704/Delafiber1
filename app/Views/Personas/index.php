<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/persona.css') ?>">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button id="btnNuevaPersona" class="btn btn-primary">➕ Nueva Persona</button>
    </div>
<div class="container form-wrapper">
    <!-- Contenedor dinámico -->
    <div id="contenido-persona">
        <!-- Lista de personas por defecto -->
        <div class="list-container form-card mt-4">
            <div class="card-header"><h5>Lista De Personas</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaPersonas" class="table table-hover table-morado">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Apellidos</th>
                                <th>Nombres</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Distrito</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personas as $persona): ?>
                            <tr>
                                <td><?= $persona->idpersona ?></td>
                                <td><?= $persona->apellidos ?></td>
                                <td><?= $persona->nombres ?></td>
                                <td><?= $persona->telprimario ?></td>
                                <td><?= $persona->email ?></td>
                                <td><?= $persona->distrito ?></td>
                                <td><span class="badge bg-success">Activo</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning">✏️</button>
                                    <button class="btn btn-sm btn-danger">🗑️</button>
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
$(document).ready(function () {

    // Abrir formulario vía AJAX
    $("#btnNuevaPersona").on("click", function () {
        $.get("<?= base_url('persona/form') ?>", function (data) {
            $("#contenido-persona").html(data);
        });
    });

    // Botón para volver a la lista dentro del formulario
    $("#contenido-persona").on("click", "#btnVolverLista", function () {
        location.reload();
    });

});
</script>
