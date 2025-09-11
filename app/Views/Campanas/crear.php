<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/campanas.css') ?>">
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h3><?= isset($campana) ? 'Editar Campaña' : 'Crear Campaña' ?></h3>
        <a href="<?= site_url('campanas') ?>" class="btn btn-secondary mt-2 mt-md-0">Volver al listado</a>
    </div>

    <form action="<?= base_url('campana/guardar') ?>" method="POST" enctype="multipart/form-data" id="form-campana">
        <?php if(isset($campana)) : ?>
            <input type="hidden" name="idcampania" value="<?= $campana['idcampania'] ?>">
        <?php endif; ?>

        <!-- Datos de la Campaña -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label>Nombre de la Campaña</label>
                        <input type="text" name="nombre" class="form-control" required value="<?= $campana['nombre'] ?? '' ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label>Presupuesto</label>
                        <input type="number" step="0.01" name="presupuesto" class="form-control" required value="<?= $campana['presupuesto'] ?? '' ?>">
                    </div>
                    <div class="col-12">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control"><?= $campana['descripcion'] ?? '' ?></textarea>
                    </div>
                    <div class="col-12 col-md-6">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" required value="<?= $campana['fecha_inicio'] ?? '' ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" required value="<?= $campana['fecha_fin'] ?? '' ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label>Público objetivo / Segmentación</label>
                        <input type="text" name="segmento" class="form-control" placeholder="Ej: Clientes VIP, zona Chincha" value="<?= $campana['segmento'] ?? '' ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label>Fecha y Hora de Creación</label>
                        <input type="text" class="form-control" value="<?= isset($campana) ? $campana['fecha_creacion'] : date('Y-m-d H:i:s') ?>" readonly>
                    </div>
                    <div class="col-12">
                        <label>Objetivos / Métricas</label>
                        <textarea name="objetivos" class="form-control"><?= $campana['objetivos'] ?? '' ?></textarea>
                    </div>
                    <div class="col-12">
                        <label>Materiales de la campaña</label>
                        <input type="file" name="archivos[]" multiple class="form-control">
                    </div>
                    <div class="col-12">
                        <label>Notas internas</label>
                        <textarea name="notas" class="form-control"><?= $campana['notas'] ?? '' ?></textarea>
                    </div>
                    <input type="hidden" name="estado" value="<?= isset($campana) ? $campana['estado'] : 'Activo' ?>">
                    <input type="hidden" name="responsable" value="<?= session()->get('idusuario') ?>">
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
            <button type="reset" class="btn btn-outline-secondary">Cancelar</button>
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
<script src="<?= base_url('js/CampanasJS/formulario.js') ?>"></script>
