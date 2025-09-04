<div class="modal fade" id="modalCrearLead" tabindex="-1" aria-labelledby="modalCrearLeadLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearLeadLabel">Registrar Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formCrearLead">
          <div class="mb-3">
            <label>Persona</label>
            <select name="idpersona" class="form-select" required>
              <?php foreach($personas as $p): ?>
                <option value="<?= $p->idpersona ?>"><?= $p->apellidos . ' ' . $p->nombres ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label>Difusión</label>
            <select name="iddifusion" class="form-select" required>
              <?php foreach($difusiones as $d): ?>
                <option value="<?= $d->iddifusion ?>"><?= $d->nombre ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label>Etapa Inicial</label>
            <select name="idetapa" class="form-select" required>
              <?php foreach($etapas as $e): ?>
                <option value="<?= $e->idetapa ?>"><?= $e->nombre ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label>Usuario Responsable</label>
            <select name="idusuarioresponsable" class="form-select" required>
              <?php foreach($usuarios as $u): ?>
                <option value="<?= $u->idusuario ?>"><?= $u->username ?> (<?= $u->nombre_persona ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <button type="submit" class="btn btn-success">Crear Lead</button>
        </form>
      </div>
    </div>
  </div>
</div>
