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

/* resumen persona */
.person-info {
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 20px;
    padding-bottom: 15px;
}
.person-info h4 { margin-bottom: 10px; font-weight: 600; }
.person-info p { color: #495057; font-size: 0.95rem; line-height: 1.5; }

/* botones etapas */
.etapa-btn {
    display: inline-block;
    padding: 10px 16px;
    margin: 5px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    color: #fff;
    transition: 0.3s;
}
.etapa-captacion { background-color: #007bff; }
.etapa-conversion { background-color: #fd7e14; }
.etapa-venta { background-color: #28a745; }
.etapa-fidelizacion { background-color: #6f42c1; }
.etapa-btn.selected { box-shadow: 0 0 0 3px rgba(0,0,0,0.15); }

/* botón guardar */
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
button.btn-submit:hover { background: #218838; }

/* botón volver */
a.btn-back {
    display: inline-block;
    margin-bottom: 15px;
    padding: 8px 16px;
    background: #6c757d;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
}
a.btn-back:hover { background: #5a6268; }
</style>

<div class="main-card">

    <!-- Botón Volver -->
    <a href="<?= base_url('leads') ?>" class="btn-back"><i class="bi bi-arrow-left"></i> Volver a Leads</a>

    <!-- Resumen Persona -->
    <div class="person-info">
        <h4>👤 <?= $persona['nombres'] . ' ' . $persona['apellidos'] ?></h4>
        <p>
            <strong>DNI:</strong> <?= $persona['dni'] ?><br>
            <strong>📞 Tel:</strong> <?= $persona['telefono'] ?><br>
            <strong>✉️ Correo:</strong> <?= $persona['correo'] ?>
        </p>
    </div>

    <form action="<?= site_url('leads/guardar') ?>" method="post" id="leadForm">
        <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">
        <input type="hidden" name="idetapa" id="idetapa" value="">

        <!-- Selección de Etapa -->
        <div class="form-group">
            <label><strong>Etapa</strong></label>
            <div>
                <?php foreach($etapas as $e): 
                    $clase = strtolower($e['nombre']); 
                    $clase = str_replace(['á','é','í','ó','ú'], ['a','e','i','o','u'], $clase);
                ?>
                    <button type="button" class="etapa-btn etapa-<?= $clase ?>" data-etapa="<?= $e['idetapa'] ?>">
                        <i class="bi bi-flag"></i> <?= $e['nombre'] ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Selección de Campaña -->
        <div class="form-group">
            <label><strong>Campaña</strong></label>
            <select name="idcampania" class="form-select" required>
                <option value="">Seleccione campaña</option>
                <?php foreach($campanas as $c): ?>
                    <option value="<?= $c['idcampania'] ?>"><?= $c['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Selección de Medio -->
        <div class="form-group">
            <label><strong>Medio</strong></label>
            <select name="idmedio" class="form-select" required>
                <option value="">Seleccione medio</option>
                <?php foreach($medios as $m): ?>
                    <option value="<?= $m['idmedio'] ?>"><?= $m['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-submit">💾 Guardar Lead</button>
    </form>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Selección de etapa
const etapaBtns = document.querySelectorAll('.etapa-btn');
const etapaInput = document.getElementById('idetapa');

etapaBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        etapaBtns.forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        etapaInput.value = btn.dataset.etapa;
    });
});

// Validación antes de enviar
document.getElementById('leadForm').addEventListener('submit', function(e){
    if(!etapaInput.value){
        e.preventDefault();
        Swal.fire({
            icon: "warning",
            title: "Etapa requerida",
            text: "Por favor seleccione una etapa para continuar.",
            confirmButtonColor: "#007bff"
        });
    } else {
        e.preventDefault(); // detener envío normal
        Swal.fire({
            icon: "question",
            title: "¿Guardar Lead?",
            text: "¿Deseas registrar este lead con la información seleccionada?",
            showCancelButton: true,
            confirmButtonText: "Sí, guardar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d"
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit(); // ahora sí enviamos el form
            }
        });
    }
});
</script>

