<?= $header ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/campanas.css') ?>">



<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="campana-table-card card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Campañas Registradas</h5>
                </div>
                <div class="card-body">
                    <table id="tablaCampanas" class="table table-campanas table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Nombre Campaña</th>
                                <th>Fechas</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($campanas as $c): ?>
                            <tr class="<?= ($c['fechafin'] < date('Y-m-d')) ? 'table' : '' ?>" 
                                data-nombre="<?= $c['nombre'] ?>" 
                                data-descripcion="<?= $c['descripcion'] ?>" 
                                data-fechainicio="<?= $c['fechainicio'] ?>" 
                                data-fechafin="<?= $c['fechafin'] ?>" 
                                data-inversion="<?= $c['inversion'] ?>" 
                                data-estado="<?= $c['estado'] ?>">
                                <td><?= $c['nombre'] ?></td>
                                <td><?= $c['fechainicio'] ?> - <?= $c['fechafin'] ?></td>
                                <td>
                                    <span class="badge <?= $c['estado']=='activo' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($c['estado']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Formulario de Crear Campaña -->
        <div class="col-lg-5">
            <?= $this->include('Campanas/crear') ?>
        </div>

    </div>
</div>

<!-- Modal para ver detalles -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-corporativo text-white">
                <h5 class="modal-title" id="detalleModalLabel">Detalle de Campaña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> <span id="modalNombre"></span></p>
                <p><strong>Descripción:</strong> <span id="modalDescripcion"></span></p>
                <p><strong>Fechas:</strong> <span id="modalFechas"></span></p>
                <p><strong>Inversión:</strong> S/ <span id="modalInversion"></span></p>
                <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablaCampanas').DataTable({
        "order": [[0, "asc"]],
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }
    });

    $('#tablaCampanas tbody').on('click', 'tr', function(e) {
        if(!$(e.target).is('a, button')) {
            var row = $(this);
            $('#modalNombre').text(row.data('nombre'));
            $('#modalDescripcion').text(row.data('descripcion'));
            $('#modalFechas').text(row.data('fechainicio') + ' - ' + row.data('fechafin'));
            $('#modalInversion').text(parseFloat(row.data('inversion')).toFixed(2));
            $('#modalEstado').text(row.data('estado'));
            var modal = new bootstrap.Modal(document.getElementById('detalleModal'));
            modal.show();
        }
    });
});
</script>
