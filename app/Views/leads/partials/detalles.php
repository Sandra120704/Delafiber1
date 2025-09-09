<div class="modal fade" id="modalLeadDetalle" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md"> 
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= htmlspecialchars($lead['nombres'] . ' ' . $lead['apellidos']) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($lead['telefono'] ?? '') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($lead['correo'] ?? '') ?></p>
        <p><strong>Campaña:</strong> <?= htmlspecialchars($lead['campania'] ?? '') ?> - <?= htmlspecialchars($lead['medio'] ?? '') ?></p>

        <hr>
        <h6>Seguimientos:</h6>
        <?php if (!empty($seguimientos)): ?>
            <ul>
                <?php foreach ($seguimientos as $s): ?>
                    <li><?= htmlspecialchars($s['comentario'] ?? '') ?> (<?= $s['fecha'] ?? '' ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay seguimientos.</p>
        <?php endif; ?>

        <hr>
        <h6>Tareas:</h6>
        <?php if (!empty($tareas)): ?>
            <ul>
                <?php foreach ($tareas as $t): ?>
                    <li><?= htmlspecialchars($t['descripcion'] ?? '') ?> - <?= htmlspecialchars($t['nombre_usuario'] ?? '') ?> (<?= $t['fecha_vencimiento'] ?? '' ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay tareas.</p>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-danger btn-desistir" data-idlead="<?= $lead['idlead'] ?>">Desistir</button>
      </div>
    </div>
  </div>
</div>
