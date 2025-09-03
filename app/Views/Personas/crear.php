<div class="card form-card mt-4">
    <div class="card-header d-flex justify-content-between">
        <button id="btnVolverLista" class="btn btn-secondary btn-sm">🔙 Volver</button>
    </div>
    <div class="card-body">
        <form id="formPersona" method="post" action="<?= base_url('persona/guardar') ?>">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nombres</label>
                    <input type="text" name="nombres" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telprimario" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <!-- Cascada para seleccionar distrito -->
                <div class="col-md-4 mb-3 mt-4">
                    <label class="form-label">Departamento</label>
                    <select id="departamento" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($departamentos as $dep): ?>
                            <option value="<?= $dep->iddepartamento ?>"><?= $dep->departamento ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mt-4">
                    <label class="form-label">Provincia</label>
                    <select id="provincia" class="form-select" disabled required>
                        <option value="">Seleccione...</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mt-4">
                    <label class="form-label">Distrito</label>
                    <select name="iddistrito" id="distrito" class="form-select" disabled required>
                        <option value="">Seleccione...</option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">💾 Guardar</button>
            </div>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

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
    $('#formPersona').on('submit', function() {
        $(this).find('button[type="submit"]').prop('disabled', true);
    });
});


</script>
