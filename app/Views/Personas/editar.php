
<div class="card form-card mt-4">
    <div class="card-header d-flex justify-content-between">
        <h5>Editar Persona</h5>
        <a href="<?= base_url('persona') ?>" class="btn btn-secondary btn-sm">🔙 Volver</a>
    </div>
    <div class="card-body">
        <form method="post" action="<?= base_url('persona/actualizar/' . $persona->idpersona) ?>">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos" value="<?= esc($persona->apellidos) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nombres</label>
                    <input type="text" name="nombres" value="<?= esc($persona->nombres) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telprimario" value="<?= esc($persona->telprimario) ?>" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="<?= esc($persona->email) ?>" class="form-control">
                </div>

                <div class="col-md-4 mb-3 mt-4">
                    <label class="form-label">Departamento</label>
                    <select id="departamento" name="iddepartamento" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($departamentos as $dep): ?>
                            <option value="<?= $dep->iddepartamento ?>" <?= ($persona->iddepartamento == $dep->iddepartamento) ? 'selected' : '' ?>>
                                <?= $dep->departamento ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3 mt-4">
                    <label class="form-label">Provincia</label>
                    <select id="provincia" name="idprovincia" class="form-select" required>
                        <!-- Se llenará con JS -->
                    </select>
                </div>

                <div class="col-md-4 mb-3 mt-4">
                    <label class="form-label">Distrito</label>
                    <select id="distrito" name="iddistrito" class="form-select" required>
                        <!-- Se llenará con JS -->
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    const personaProvincia = "<?= $persona->idprovincia ?>";
    const personaDistrito = "<?= $persona->iddistrito ?>";

    // Cargar provincias al cargar la página
    const depId = $("#departamento").val();
    if(depId) {
        $.get('<?= base_url('persona/getProvincias') ?>/' + depId, function(data) {
            $('#provincia').html('<option value="">Seleccione...</option>');
            data.forEach(p => {
                const selected = (p.idprovincia == personaProvincia) ? 'selected' : '';
                $('#provincia').append(`<option value="${p.idprovincia}" ${selected}>${p.provincia}</option>`);
            });
            $('#provincia').prop('disabled', false);

            // Cargar distritos correspondientes
            if(personaProvincia) {
                $.get('<?= base_url('persona/getDistritos') ?>/' + personaProvincia, function(distritos) {
                    $('#distrito').html('<option value="">Seleccione...</option>');
                    distritos.forEach(d => {
                        const selected = (d.iddistrito == personaDistrito) ? 'selected' : '';
                        $('#distrito').append(`<option value="${d.iddistrito}" ${selected}>${d.distrito}</option>`);
                    });
                    $('#distrito').prop('disabled', false);
                }, 'json');
            }
        }, 'json');
    }

    // Actualizar provincias cuando cambia el departamento
    $('#departamento').on('change', function() {
        const idDepartamento = $(this).val();
        $('#provincia').html('<option>Cargando...</option>').prop('disabled', true);
        $('#distrito').html('<option value="">Seleccione...</option>').prop('disabled', true);

        if(idDepartamento) {
            $.get('<?= base_url('persona/getProvincias') ?>/' + idDepartamento, function(data) {
                $('#provincia').html('<option value="">Seleccione...</option>');
                data.forEach(p => $('#provincia').append(`<option value="${p.idprovincia}">${p.provincia}</option>`));
                $('#provincia').prop('disabled', false);
            }, 'json');
        }
    });

    // Actualizar distritos cuando cambia la provincia
    $('#provincia').on('change', function() {
        const idProvincia = $(this).val();
        $('#distrito').html('<option>Cargando...</option>').prop('disabled', true);

        if(idProvincia) {
            $.get('<?= base_url('persona/getDistritos') ?>/' + idProvincia, function(data) {
                $('#distrito').html('<option value="">Seleccione...</option>');
                data.forEach(d => $('#distrito').append(`<option value="${d.iddistrito}">${d.distrito}</option>`));
                $('#distrito').prop('disabled', false);
            }, 'json');
        }
    });

});
</script>
