<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/persona.css') ?>">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button id="btnNuevaPersona" class="btn btn-primary"> Nueva Persona</button>
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
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $persona->idpersona ?>">✏️</button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $persona->idpersona ?>">🗑️</button>
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

    // ABRIR FORMULARIO NUEVA PERSONA
    $("#btnNuevaPersona").on("click", function () {
        $.get("<?= base_url('persona/form') ?>", function (data) {
            $("#contenido-persona").html(data);
        });
    });

    // EDITAR PERSONA (carga el formulario de editar en el mismo contenedor)
    $("#tablaPersonas").on("click", ".btn-edit", function () {
        const id = $(this).data("id");
        $.get("<?= base_url('persona/editar/') ?>" + id, function (data) {
            $("#contenido-persona").html(data);
        });
    });

    // ELIMINAR PERSONA
    $("#tablaPersonas").on("click", ".btn-delete", function () {
        const id = $(this).data("id");
        if(confirm("¿Seguro de eliminar esta persona?")) {
            $.post("<?= base_url('persona/eliminar') ?>", {idpersona: id}, function(res) {
                alert(res.mensaje);
                location.reload();
            }, "json");
        }
    });

    // VOLVER A LISTA desde cualquier formulario cargado
    $("#contenido-persona").on("click", "#btnVolverLista", function (e) {
        e.preventDefault();
        $.get("<?= base_url('persona') ?>", function (data) {
            $("#contenido-persona").html(data);
        });
    });

});
</script>
