<?= $header ?>

<style>
.swal2-container {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
</style>

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

                <div class="row g-2">
                    <div class="col-md-6 mb-2">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" class="form-control" name="apellidos" id="apellidos" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="nombres">Nombres</label>
                        <input type="text" class="form-control" name="nombres" id="nombres" required>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-8 mb-2">
                        <label for="correo">Correo Electronico</label>
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
                                <option value="<?= $d['iddistrito'] ?>"><?= $d['nombre'] ?></option>
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
<script>
  const base_url = "<?= base_url(); ?>";
</script>
<script src="<?= base_url('js/personas.js') ?>"></script>
 <!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>