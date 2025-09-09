
<div class="modal-header border=1">
    <h5 class="modal-title">Convertir Persona a Lead</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
</div>
<div class="modal-body">
    <form id="leadFormModal" action="<?= site_url('leads/guardar') ?>" method="post">
        <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">
        <input type="hidden" name="idetapa" value="<?= $etapas[0]['idetapa'] ?>"> <!-- Tomamos la primera etapa por default -->

        <p><strong>Nombre:</strong> <?= $persona['nombres'] . ' ' . $persona['apellidos'] ?></p>
        <p><strong>DNI:</strong> <?= $persona['dni'] ?></p>
        <p><strong>Tel:</strong> <?= $persona['telefono'] ?></p>
        <p><strong>Email:</strong> <?= $persona['correo'] ?></p>

        <!-- Selección de campaña -->
        <div class="mb-3">
            <label><strong>Campaña</strong></label>
            <select name="idcampania" class="form-control" required>
                <option value="">Seleccione campaña</option>
                <?php foreach($campanas as $c): ?>
                    <option value="<?= $c['idcampania'] ?>"><?= $c['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Selección de medio -->
        <div class="mb-3">
            <label><strong>Medio</strong></label>
            <select name="idmedio" class="form-control" required>
                <option value="">Seleccione medio</option>
                <?php foreach($medios as $m): ?>
                    <option value="<?= $m['idmedio'] ?>"><?= $m['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success w-100">💾 Guardar Lead</button>
    </form>
</div>

<script>
document.getElementById('leadFormModal').addEventListener('submit', function(e){
    e.preventDefault();
    const form = this;
    Swal.fire({
        icon: 'question',
        title: '¿Guardar Lead?',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then(result => {
        if(result.isConfirmed){
            form.submit();
        }
    });
});
</script>
