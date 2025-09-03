<div class="card mt-4">
    <div class="card-header d-flex justify-content-between">
        <h5><?= isset($campania) ? 'Editar Campaña' : 'Nueva Campaña' ?></h5>
        <button id="btnVolverLista" class="btn btn-secondary btn-sm">🔙 Volver</button>
    </div>
    <div class="card-body">
        <form id="formCampana" action="<?= base_url('campana/guardar') ?>" method="post">
            <?php if(isset($campania)): ?>
                <input type="hidden" name="idcampania" value="<?= $campania->idcampania ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" required value="<?= $campania->nombre ?? '' ?>">
            </div>
            <div class="mb-3">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control"><?= $campania->descripcion ?? '' ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fechainicio" class="form-control" required value="<?= $campania->fechainicio ?? '' ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Fecha Fin</label>
                    <input type="date" name="fechafin" class="form-control" required value="<?= $campania->fechafin ?? '' ?>">
                </div>
            </div>
            <div class="mb-3">
                <label>Inversión</label>
                <input type="number" step="0.01" name="inversion" class="form-control" value="<?= $campania->inversion ?? '' ?>">
            </div>
            <div>
                <button type="submit" class="btn btn-success"><?= isset($campania) ? 'Guardar Cambios' : 'Registrar' ?></button>
            </div>
            
        </form>
    </div>
</div>

<script>
$(function(){
    // Volver a la lista
    $("#btnVolverLista").on("click", function(){
        $.get("<?= site_url('campana') ?>", function(html){
            $("#contenido-campana").html(html); // coincide con el contenedor
        });
    });

    // Guardar formulario
    $("#formCampana").on("submit", function(e){
        e.preventDefault();
        const form = $(this);
        const btn = form.find('button[type="submit"]');
        btn.prop('disabled', true); // deshabilitar mientras procesa

        $.post(form.attr("action"), form.serialize(), function(res){
            alert(res.mensaje);
            if(res.success){
                $.get("<?= site_url('campana') ?>", function(html){
                    $("#contenido-campana").html(html);
                });
            }
        }, "json")
        .fail(() => alert("Error al guardar"))
        .always(() => btn.prop('disabled', false)); // volver a habilitar
    });
});

</script>
