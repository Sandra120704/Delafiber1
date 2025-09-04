<div class="modal fade" id="modalLead" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formLead" action="<?= site_url('lead/guardar') ?>" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Convertir en Lead</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="idpersona" value="<?= $persona->idpersona ?>">

            <div class="mb-3">
                <label>Campaña / Difusión</label>
                <select name="iddifusion" class="form-select" required>
                    <?php foreach($difusiones as $d): ?>
                        <option value="<?= $d->iddifusion ?>"><?= htmlspecialchars($d->campania) ?> - <?= htmlspecialchars($d->medio) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Responsable</label>
                <select name="idusuarioresponsable" class="form-select" required>
                    <?php foreach($usuarios as $u): ?>
                        <option value="<?= $u->idusuario ?>"><?= htmlspecialchars($u->nombres . ' ' . $u->apellidos) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Etapa Inicial</label>
                <select name="idetapa" class="form-select" required>
                    <?php foreach($etapas as $e): ?>
                        <option value="<?= $e->idetapa ?>"><?= htmlspecialchars($e->nombreetapa) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Guardar Lead</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>
