<?= $header ?>

<div class="container mt-2">
    <div class="my-2">
        <h3>Registro De Personas</h3>
        <a href="<?= base_url('personas');?>">Lista de personas</a>

    </div>

    <form action="<?= base_url('personas/guardar') ?>" id="form-persona" method="POST" autocomplete="off">
        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <label for="">Buscar DNI</label><small class="d-none" id="searching">Por favor espere</small>
                    <div class="input-group">
                        <input type="text" class="form-control" name="dni" id="dni" maxlength="8" minlength="8" required autofocus>
                        <button class="btn btn-outline-success" type="button" id="buscar-dni">Buscar</button>
                    </div>
                </div>

                <!-- Apellidos Registro -->
                 <div class=" row g-2">
                    <div class="col-md-6 mb-2">
                        <label for="apellidos">Apellidos </label>
                        <input type="text" class="form-control" name="apellidos" id="apellidos" required>
                    </div>
                    <!-- Nombres Registros -->
                    <div class="col-md-6 mb-2">
                        <label for="nombres">Nombres</label>
                        <input type="text" class="form-control" name="nombres" id="nombres" required>
                    </div>
                 </div>
                 <div class="row g-2">
                    <div class="col-md-8 mb-2">
                        <label for="Correo">Correo Electronico</label>
                        <input type="text" class="form-control" name="correo" id="correo">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="telefono">Telefono</label>
                        <input type="text" class="form-control" name="telefono" id="telefono" maxlength="9" pattern="[0-9]*" title="Solo se permiten números" required>
                    </div>
                 </div>
                 <div class="row g-2">
                    <div class="col-md-8 mb-2">
                        <label for="direccion">Direccion</label>
                        <input type="text" class="form-control" name="direccion" id="direccion">
                    </div>
                    <div class="col-md-4 mb-2">
                    <label for="iddistrito">Distrito</label>
                    <select class="form-control" name="iddistrito" id="iddistrito" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($distritos as $d): ?>
                            <option value="<?= $d['iddistrito'] ?>">
                                <?= $d['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-sm btn-outline-secondary" type="reset">Cancelar</button>
                <button class="btn btn-sm btn-primary" type="submit">Guardar</button>
            </div>
        </div>

    </form>

</div>

<?= $footer ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dniInput = document.getElementById('dni');
    const btnBuscar = document.getElementById('buscar-dni');
    const apellidosInput = document.getElementById('apellidos');
    const nombresInput = document.getElementById('nombres');
    const buscando = document.getElementById('searching');

    btnBuscar.addEventListener('click', async () => {
        if(!dniInput.value){
            alert('Ingrese un DNI');
            return;
        }

        buscando.classList.remove('d-none');

        try {
            const res = await fetch(`<?= base_url() ?>api/personas/buscardni/${dniInput.value}`);
            if(!res.ok) throw new Error('Error en la solicitud');

            const data = await res.json();
            buscando.classList.add('d-none');

            if(data.success){
                apellidosInput.value = `${data.apepaterno} ${data.apematerno}`;
                nombresInput.value = data.nombres;
            } else {
                apellidosInput.value = '';
                nombresInput.value = '';
                alert(data.message || 'No se encontró la persona');
            }
        } catch(err){
            buscando.classList.add('d-none');
            console.error(err);
            alert('Error al consultar el DNI');
        }
    });
});
</script>

