<?= $header ?>

<style>
/* tarjeta principal */
.main-card {
    max-width: 700px;
    margin: 40px auto;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(12, 38, 63, 0.08);
    background: #ffffff;
}

/* sección de persona */
.person-info {
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 20px;
    padding-bottom: 15px;
}

.person-info h4 {
    margin-bottom: 5px;
}

.person-info p {
    color: #6c757d;
    font-size: 0.9rem;
}

/* campos del formulario */
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-weight: 500;
    margin-bottom: 5px;
    display: block;
}

.form-group select {
    width: 100%;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ced4da;
    font-size: 0.95rem;
}

button.btn-submit {
    background: #28a745;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: 0.3s;
}

button.btn-submit:hover {
    background: #218838;
}
</style>

<div class="main-card">

    <div class="person-info">
        <h4>Registrar Lead para: <?= $persona['nombres'] . ' ' . $persona['apellidos'] ?></h4>
        <p>DNI: <?= $persona['dni'] ?> | Tel: <?= $persona['telefono'] ?> | Correo: <?= $persona['correo'] ?></p>
    </div>

    <form action="<?= site_url('leads/guardar') ?>" method="post">
        <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">

        <div class="form-group">
            <label>Campaña</label>
            <select name="idcampania" required>
                <option value="">Seleccione campaña</option>
                <?php foreach($campanas as $c): ?>
                    <option value="<?= $c['idcampania'] ?>"><?= $c['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Medio</label>
            <select name="idmedio" required>
                <option value="">Seleccione medio</option>
                <?php foreach($medios as $m): ?>
                    <option value="<?= $m['idmedio'] ?>"><?= $m['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Etapa inicial</label>
            <select name="idetapa" required>
                <option value="">Seleccione etapa</option>
                <?php foreach($etapas as $e): ?>
                    <option value="<?= $e['idetapa'] ?>"><?= $e['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-submit">Guardar Lead</button>
    </form>

</div>

<?= $footer ?>
