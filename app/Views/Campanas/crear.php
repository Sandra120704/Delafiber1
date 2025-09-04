<link rel="stylesheet" href="<?= base_url('css/persona.css') ?>">
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between">
        <h5><?= isset($campania) ? 'Editar Campaña' : 'Nueva Campaña' ?></h5>
        <button id="btnVolverLista" class="btn btn-secondary btn-sm">🔙 Volver</button>
    </div>
    <div class="card-body">
        <form id="formCampana" action="<?= site_url('campana/guardar') ?>" method="post">
            <?php if(isset($campania)): ?>
                <input type="hidden" name="idcampania" value="<?= $campania->idcampania ?>">
            <?php endif; ?>
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" required value="<?= $campania->nombre ?? '' ?>">

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

            <div class="mb-3">
                <label>Medios de Difusión</label>
                <div class="d-flex flex-wrap">
                    <?php foreach($medios as $m): 
                        $checked = (isset($difusiones_asociadas) && in_array($m->idmedio, $difusiones_asociadas)) ? 'checked' : '';
                    ?>
                        <div class="form-check me-3">
                            <input class="form-check-input" 
                                    type="radio" 
                                    name="medio" 
                                    value="<?= $m->idmedio ?>" 
                                    id="medio<?= $m->idmedio ?>" 
                                    <?= $checked ?>>
                            <label class="form-check-label" for="medio<?= $m->idmedio ?>">
                                <?= $m->medio ?> (<?= $m->tipo_medio ?>)
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
            <div>
                <button type="submit" class="btn btn-success"><?= isset($campania) ? 'Guardar Cambios' : 'Registrar' ?></button>
            </div>
        </form>
     </div>
    </div>
</div>
<script>
    window.base_url = "<?= site_url('') ?>"; // termina con /
</script>
<script src="<?= base_url('js/campana.js') ?>"></script>

<script>
$(function(){
    // Volver a la lista
    $("#btnVolverLista").on("click", function(){
        $.get("<?= site_url('campanas') ?>", function(html){
            $("#contenido-campanas").html(html);
        });
    });
});
</script>

