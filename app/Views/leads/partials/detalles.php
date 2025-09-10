<!-- Cabecera del modal -->
<div class="modal-header">
    <h5 class="modal-title">
        <?= esc($lead['nombres'] ?? '-') ?> <?= esc($lead['apellidos'] ?? '-') ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<!-- Cuerpo del modal -->
<div class="modal-body">

    <div class="mb-3">
        <strong>Teléfono:</strong> <?= esc($lead['telefono'] ?? '-') ?><br>
        <strong>Correo:</strong> <?= esc($lead['correo'] ?? '-') ?><br>
        <strong>Campaña:</strong> <?= esc($lead['campania'] ?? '-') ?><br>
        <strong>Medio:</strong> <?= esc($lead['medio'] ?? '-') ?><br>
        <strong>Usuario asignado:</strong> <?= esc($lead['usuario'] ?? '-') ?>
    </div>

    <h6>Tareas</h6>
    <ul id="listaTareas">
        <?php if (!empty($tareas)): ?>
            <?php foreach ($tareas as $t): ?>
                <li><?= esc($t['descripcion']) ?> - <?= date('d/m/Y H:i', strtotime($t['fecha_registro'])) ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="text-muted">No hay tareas registradas</li>
        <?php endif; ?>
    </ul>

    <form id="tareaForm">
        <input type="hidden" id="tareaIdLead" value="<?= $lead['idlead'] ?>">
        <input type="text" name="descripcion" placeholder="Nueva tarea" class="form-control mb-2" required>
        <button type="submit" class="btn btn-primary btn-sm">Agregar tarea</button>
    </form>

    <h6 class="mt-3">Seguimientos</h6>
    <ul id="listaSeguimientos">
        <?php if (!empty($seguimientos)): ?>
            <?php foreach ($seguimientos as $s): ?>
                <li><?= esc($s['comentario']) ?> - <?= date('d/m/Y H:i', strtotime($s['fecha'])) ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="text-muted">No hay seguimientos registrados</li>
        <?php endif; ?>
    </ul>

    <form id="seguimientoForm" class="mt-2">
        <div class="mb-2">
            <select name="idmodalidad" class="form-select" required>
                <option value="">Seleccione modalidad</option>
                <?php foreach($modalidades as $m): ?>
                    <option value="<?= $m['idmodalidad'] ?>"><?= esc($m['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <textarea name="comentario" class="form-control mb-2" placeholder="Nuevo seguimiento" required></textarea>
        <button type="submit" class="btn btn-success btn-sm">Agregar seguimiento</button>
    </form>

    <button id="btnDesistirLead" class="btn btn-danger mt-3">Desistir Lead</button>

</div>
