<?= $header ?>

<style>
/* estilos rápidos para que se vea centrado y moderno */
.main-card {
  max-width: 1100px;
  width: 100%;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(12, 38, 63, .08);
  background: #ffffff;
}
.person-avatar {
  width:44px;
  height:44px;
  border-radius:50%;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-weight:700;
  font-size:14px;
}
.table-hover-row tr:hover { background:#fbfcfd; }
.small-muted { font-size:.85rem; color:#6c757d; }
</style>

<div class="d-flex justify-content-center py-5">
  <div class="main-card p-4">

    <!-- header: título + botón -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3 class="mb-0">Personas</h3>
        <div class="small-muted">Listado de contactos registrados</div>
      </div>
      <div class="d-flex gap-2">
        <a href="<?= site_url('personas/crear') ?>" class="btn btn-primary">+ Crear persona</a>
      </div>
    </div>

    <!-- buscador simple -->
    <form class="mb-3" method="get" action="<?= site_url('personas') ?>">
      <div class="input-group">
        <input name="q" value="<?= esc($_GET['q'] ?? '') ?>" class="form-control" placeholder="Buscar por nombre, DNI, teléfono o correo">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
      </div>
    </form>

    <!-- tabla responsive dentro de tarjeta -->
    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead class="table-light">
          <tr>
            <th style="width:60px">#</th>
            <th>Contacto</th>
            <th>DNI</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th style="width:170px">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (! empty($personas)): foreach($personas as $p): ?>
            <tr>
              <td><?= esc($p['idpersona']) ?></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="me-3">
                    <!-- avatar iniciales -->
                    <div class="person-avatar mt-2" style="background:#8e44ad; margin-top: 2px;" >
                      <?= strtoupper(substr($p['nombres'],0,1) . (isset($p['apellidos'][0])?substr($p['apellidos'],0,1):'')) ?>
                    </div>
                  </div>
                  <div>
                    <div class="fw-bold"><?= esc($p['nombres']).' '.esc($p['apellidos']) ?></div>
                    <div class="small-muted"><?= esc($p['direccion'] ?? '') ?></div>
                  </div>
                </div>
              </td>
              <td><?= esc($p['dni']) ?></td>
              <td><a class="text-decoration-none" href="tel:<?= esc($p['telefono']) ?>"><?= esc($p['telefono']) ?></a></td>
              <td><a class="text-decoration-none" href="mailto:<?= esc($p['correo']) ?>"><?= esc($p['correo']) ?></a></td>
              <td>
                <a href="<?= site_url('personas/edit/'.$p['idpersona']) ?>" class="btn btn-sm btn-outline-warning">Editar</a>
                <a href="<?= site_url('personas/delete/'.$p['idpersona']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar persona?')">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr>
              <td colspan="6" class="text-center py-4 small-muted">No hay personas registradas.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>