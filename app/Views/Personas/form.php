<div class="card form-card mt-4">
    <div class="card-header d-flex justify-content-between">
        <h5><?= isset($persona) ? 'Editar Persona' : 'Nueva Persona' ?></h5>
        <!-- <button id="btnVolverLista" class="btn btn-secondary btn-sm">🔙 Volver</button> -->
    </div>
    <div class="card-body">
        <form method="post" action="<?= isset($persona) ? base_url('persona/actualizar/' . $persona->idpersona) : base_url('persona/guardar') ?>">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos" value="<?= isset($persona) ? esc($persona->apellidos) : '' ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nombres</label>
                    <input type="text" name="nombres" value="<?= isset($persona) ? esc($persona->nombres) : '' ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telprimario" value="<?= isset($persona) ? esc($persona->telprimario) : '' ?>" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="<?= isset($persona) ? esc($persona->email) : '' ?>" class="form-control">
                </div>

                <div class="col-md-4 mb-3 mt-4">
                    <label class="form-label">Departamento</label>
                    <select id="departamento" name="iddepartamento" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($departamentos as $dep): ?>
                            <option value="<?= $dep['iddepartamento'] ?>" 
                                <?= (isset($persona) && $persona->iddepartamento == $dep['iddepartamento']) ? 'selected' : '' ?>>
                                <?= $dep['departamento'] ?>
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
                <button type="submit" class="btn btn-success">💾 <?= isset($persona) ? 'Guardar Cambios' : 'Registrar' ?></button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    const personaProvincia = "<?= isset($persona) ? $persona->idprovincia : '' ?>";
    const personaDistrito = "<?= isset($persona) ? $persona->iddistrito : '' ?>";

    // Cargar provincias si hay departamento
    const depId = $("#departamento").val();
    if(depId){
        $.get('<?= base_url('persona/getProvincias') ?>/' + depId, function(data){
            $('#provincia').html('<option value="">Seleccione...</option>');
            data.forEach(p => {
                const selected = (p.idprovincia == personaProvincia) ? 'selected' : '';
                $('#provincia').append(`<option value="${p.idprovincia}" ${selected}>${p.provincia}</option>`);
            });

            if(personaProvincia){
                $.get('<?= base_url('persona/getDistritos') ?>/' + personaProvincia, function(distritos){
                    $('#distrito').html('<option value="">Seleccione...</option>');
                    distritos.forEach(d => {
                        const selected = (d.iddistrito == personaDistrito) ? 'selected' : '';
                        $('#distrito').append(`<option value="${d.iddistrito}" ${selected}>${d.distrito}</option>`);
                    });
                }, 'json');
            }
        }, 'json');
    }

    // Cambiar provincias al seleccionar departamento
    $('#departamento').on('change', function(){
        const idDep = $(this).val();
        $('#provincia').html('<option>Cargando...</option>');
        $('#distrito').html('<option value="">Seleccione...</option>');

        if(idDep){
            $.get('<?= base_url('persona/getProvincias') ?>/' + idDep, function(data){
                $('#provincia').html('<option value="">Seleccione...</option>');
                data.forEach(p => $('#provincia').append(`<option value="${p.idprovincia}">${p.provincia}</option>`));
            }, 'json');
        }
    });

    // Cambiar distritos al seleccionar provincia
    $('#provincia').on('change', function(){
        const idProv = $(this).val();
        $('#distrito').html('<option>Cargando...</option>');
        if(idProv){
            $.get('<?= base_url('persona/getDistritos') ?>/' + idProv, function(data){
                $('#distrito').html('<option value="">Seleccione...</option>');
                data.forEach(d => $('#distrito').append(`<option value="${d.iddistrito}">${d.distrito}</option>`));
            }, 'json');
        }
    });
});
</script>
