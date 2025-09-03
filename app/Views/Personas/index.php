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
                                    <a href="<?= base_url('personas/editar/' . $persona->idpersona) ?>" class="btn btn-sm btn-warning">✏️</a>
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

    // Abrir formulario nueva persona
    $("#btnNuevaPersona").on("click", function () {
        $.get("<?= site_url('persona/form') ?>", function (data) {
            $("#contenido-persona").html(data);
        });
    });

    // Guardar persona vía AJAX
    $("#contenido-persona").on("submit", "#formPersona", function (e) {
        e.preventDefault();
        const form = $(this);
        
        $.post(form.attr("action"), form.serialize(), function(res) {
            alert(res.mensaje); // Mensaje de éxito o error
            
            if(res.success){
                // Cargar la lista de personas en el mismo div
                $.get("<?= site_url('personas') ?>", function(data){
                    $("#contenido-persona").html(data);
                });
            }
        }, "json");
    });

    // Volver a la lista sin recargar
    $("#contenido-persona").on("click", "#btnVolverLista", function () {
        $.get("<?= site_url('personas') ?>", function(data){
            $("#contenido-persona").html(data);
        });
    });

    // Editar persona
    $("#contenido-persona").on("click", ".btn-edit", function(){
        const id = $(this).data("id");
        $.get("<?= site_url('persona/form') ?>/" + id, function(data){
            $("#contenido-persona").html(data);
        });
    });

    // Eliminar persona
    $("#contenido-persona").on("click", ".btn-delete", function() {
        const id = $(this).data("id");
        if(confirm("¿Seguro de eliminar esta persona?")) {
            $.post("<?= site_url('persona/eliminar') ?>", {idpersona: id}, function(res) {
                alert(res.mensaje);
                $.get("<?= site_url('personas') ?>", function(data){
                    $("#contenido-persona").html(data);
                });
            }, "json");
        }
    });

});

</script>
