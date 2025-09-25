<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/campanas.css') ?>">
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h3><?= isset($campana) ? 'Editar Campaña' : 'Crear Campaña' ?></h3>
        <a href="<?= site_url('campanas') ?>" class="btn btn-secondary mt-2 mt-md-0">Volver al listado</a>
    </div>

    <form action="<?= base_url('campanas/guardar') ?>" method="POST" id="form-campana">
        <?php if(isset($campana)) : ?>
            <input type="hidden" name="idcampania" value="<?= $campana['idcampania'] ?>">
        <?php endif; ?>

        <!-- Datos de la Campaña -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="nombre" class="form-label">Nombre de la Campaña *</label>
               <input type="text" name="nombre" id="nombre" class="form-control" required 
                   value="<?= $campana['nombre'] ?? '' ?>" 
                   placeholder="Ej: Campaña Navidad 2024">
               <div class="invalid-feedback" id="error-nombre"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="presupuesto" class="form-label">Presupuesto *</label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                <input type="number" step="0.01" name="presupuesto" id="presupuesto" 
                    class="form-control" required min="0"
                    value="<?= $campana['presupuesto'] ?? '' ?>" 
                    placeholder="0.00">
                <div class="invalid-feedback" id="error-presupuesto"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3"
                                  placeholder="Describe los objetivos y características de la campaña..."><?= $campana['descripcion'] ?? '' ?></textarea>
                        <div class="invalid-feedback" id="error-descripcion"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio *</label>
               <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required 
                   value="<?= $campana['fecha_inicio'] ?? '' ?>">
               <div class="invalid-feedback" id="error-fecha-inicio"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="fecha_fin" class="form-label">Fecha Fin *</label>
               <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required 
                   value="<?= $campana['fecha_fin'] ?? '' ?>">
               <div class="invalid-feedback" id="error-fecha-fin"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="responsable" class="form-label">Responsable</label>
                        <select name="responsable" id="responsable" class="form-select">
                            <option value="">Seleccionar responsable</option>
                            <?php if(isset($usuarios)): ?>
                                <?php foreach($usuarios as $usuario): ?>
                                    <option value="<?= $usuario['idusuario'] ?>" 
                                            <?= ($campana['responsable'] ?? session()->get('idusuario')) == $usuario['idusuario'] ? 'selected' : '' ?>>
                                        <?= $usuario['nombre_completo'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <!-- <div class="col-12 col-md-6">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="Activa" <?= ($campana['estado'] ?? 'Activa') == 'Activa' ? 'selected' : '' ?>>Activa</option>
                            <option value="Inactiva" <?= ($campana['estado'] ?? '') == 'Inactiva' ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div> -->
                    <?php if(isset($campana)): ?>
                    <div class="col-12">
                        <label class="form-label">Fecha de Creación</label>
                        <input type="text" class="form-control" 
                               value="<?= date('d/m/Y H:i', strtotime($campana['fecha_creacion'])) ?>" readonly>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Difusión en Medios -->
        <div class="card mb-3">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <span>Difusión en Medios</span>
                <div class="mt-2 mt-md-0">
                    <button type="button" class="btn btn-sm btn-primary me-2 mb-2 mb-md-0" id="agregarMedioBtn">+ Agregar Medio</button>
                    <button type="button" class="btn btn-sm btn-success" id="nuevoMedioBtn">+ Nuevo Medio</button>
                </div>
            </div>
            <div class="card-body">
                <div id="mediosContainer">
                    <div class="row g-2 mb-2 medio-row">
                        <div class="col-12 col-md-6">
                            <select name="medios[]" class="form-control" required>
                                <option value="">Seleccione Medio</option>
                                <?php foreach($medios as $m): ?>
                                    <option value="<?= $m['idmedio'] ?>"><?= $m['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <input type="number" step="0.01" name="inversion[]" class="form-control" placeholder="Inversión">
                        </div>
                        <div class="col-12 col-md-2 d-grid">
                            <button type="button" class="btn btn-danger btn-sm eliminarMedio">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mb-4">
                    <button type="reset" class="btn btn-outline-secondary" title="Limpia todos los campos">Limpiar formulario</button>
            <button type="submit" class="btn btn-primary">Guardar Campaña</button>
        </div>
    </form>
</div>

<!-- Modal Nuevo Medio -->
<div class="modal fade" id="modalNuevoMedio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Medio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nombreMedio" class="form-label">Nombre del Medio</label>
                    <input type="text" id="nombreMedio" class="form-control" placeholder="Nombre del medio">
                </div>
                <div class="mb-3">
                    <label for="descMedio" class="form-label">Descripción</label>
                    <textarea id="descMedio" class="form-control" placeholder="Descripción opcional"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="guardarMedioBtn">Guardar Medio</button>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const BASE_URL = '<?= base_url() ?>';

    // Validación visual de fechas
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const errorFechaFin = document.getElementById('error-fecha-fin');

    function validarFechas() {
        let valido = true;
        errorFechaFin.textContent = '';
        fechaFin.classList.remove('is-invalid');
        if (fechaInicio.value && fechaFin.value) {
            if (fechaInicio.value > fechaFin.value) {
                errorFechaFin.textContent = 'La fecha fin debe ser posterior a la fecha inicio';
                fechaFin.classList.add('is-invalid');
                valido = false;
            }
        }
        return valido;
    }

    fechaInicio.addEventListener('change', validarFechas);
    fechaFin.addEventListener('change', validarFechas);

    // Validación simple de campos obligatorios
    function validarCampos() {
        let valido = true;
        // Nombre
        const nombre = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        if (!nombre.value.trim()) {
            errorNombre.textContent = 'El nombre es obligatorio';
            nombre.classList.add('is-invalid');
            valido = false;
        } else {
            errorNombre.textContent = '';
            nombre.classList.remove('is-invalid');
        }
        // Presupuesto
        const presupuesto = document.getElementById('presupuesto');
        const errorPresupuesto = document.getElementById('error-presupuesto');
        if (!presupuesto.value || parseFloat(presupuesto.value) < 0) {
            errorPresupuesto.textContent = 'El presupuesto debe ser mayor o igual a 0';
            presupuesto.classList.add('is-invalid');
            valido = false;
        } else {
            errorPresupuesto.textContent = '';
            presupuesto.classList.remove('is-invalid');
        }
        // Descripción
        const descripcion = document.getElementById('descripcion');
        const errorDescripcion = document.getElementById('error-descripcion');
        if (!descripcion.value.trim()) {
            errorDescripcion.textContent = 'La descripción es obligatoria';
            descripcion.classList.add('is-invalid');
            valido = false;
        } else {
            errorDescripcion.textContent = '';
            descripcion.classList.remove('is-invalid');
        }
        // Fechas
        valido = validarFechas() && valido;
        return valido;
    }

    // Manejo del formulario principal
    const form = document.getElementById('form-campana');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!validarCampos()) {
                return;
            }
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Guardando...';
            submitBtn.disabled = true;
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    window.location.href = `${BASE_URL}campanas?success=1&message=${encodeURIComponent(result.message || 'Campaña guardada exitosamente')}`;
                } else {
                    // Mostrar errores específicos
                    if (result.errors) {
                        Object.entries(result.errors).forEach(([campo, mensaje]) => {
                            const errorDiv = document.getElementById('error-' + campo);
                            const input = document.getElementById(campo);
                            if (errorDiv && input) {
                                errorDiv.textContent = mensaje;
                                input.classList.add('is-invalid');
                            }
                        });
                    } else {
                        alert('Error: ' + (result.message || 'Error desconocido'));
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión al guardar la campaña');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Gestión de medios dinámicos
    let medioCounter = 1;
    document.getElementById('agregarMedioBtn')?.addEventListener('click', () => {
        const container = document.getElementById('mediosContainer');
        const newRow = container.querySelector('.medio-row').cloneNode(true);
        newRow.querySelectorAll('input, select').forEach(input => {
            input.value = '';
        });
        container.appendChild(newRow);
        medioCounter++;
    });
    document.getElementById('mediosContainer')?.addEventListener('click', (e) => {
        if (e.target.classList.contains('eliminarMedio')) {
            const rows = document.querySelectorAll('.medio-row');
            if (rows.length > 1) {
                e.target.closest('.medio-row').remove();
            } else {
                alert('Debe mantener al menos un medio');
            }
        }
    });

    // Modal nuevo medio
    const modalNuevoMedio = new bootstrap.Modal(document.getElementById('modalNuevoMedio'));
    document.getElementById('nuevoMedioBtn')?.addEventListener('click', () => {
        modalNuevoMedio.show();
    });
    document.getElementById('guardarMedioBtn')?.addEventListener('click', async () => {
        const nombre = document.getElementById('nombreMedio').value.trim();
        const descripcion = document.getElementById('descMedio').value.trim();
        if (!nombre) {
            alert('El nombre del medio es obligatorio');
            return;
        }
        try {
            const response = await fetch(`${BASE_URL}medios/guardar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `nombre=${encodeURIComponent(nombre)}&descripcion=${encodeURIComponent(descripcion)}`
            });
            const result = await response.json();
            if (result.success) {
                document.querySelectorAll('select[name="medios[]"]').forEach(select => {
                    const option = new Option(nombre, result.id);
                    select.add(option);
                    select.value = result.id; // Selecciona el nuevo medio automáticamente
                });
                document.getElementById('nombreMedio').value = '';
                document.getElementById('descMedio').value = '';
                modalNuevoMedio.hide();
                alert('Medio creado exitosamente');
            } else {
                alert('Error al crear el medio: ' + (result.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión al crear el medio');
        }
    });
});
</script>
