<?= $header ?>

<style>
/* tarjeta principal */
.main-card {
    max-width: 1100px;
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(12, 38, 63, .08);
    background: #ffffff;
}

/* avatar persona */
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

.table-hover tbody tr:hover { background:#f8f9fa; }
.small-muted { font-size:.85rem; color:#6c757d; }
</style>
<div class="d-flex justify-content-center py-5">
  <div class="main-card p-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3 class="mb-0">Personas</h3>
        <div class="small-muted">Listado de contactos registrados</div>
      </div>
      <div>
        <a href="<?= site_url('personas/crear') ?>" class="btn btn-primary">+ Crear persona</a>
      </div>
    </div>

    <!-- Buscador -->
    <form class="mb-3" method="get" action="<?= site_url('personas') ?>">
      <div class="input-group">
        <input name="q" value="<?= esc($_GET['q'] ?? '') ?>" class="form-control" placeholder="Buscar por nombre, DNI, teléfono o correo">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
      </div>
    </form>

    <!-- Tabla de personas -->
    <div class="table-responsive">
      <table class="table table-sm table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Contacto</th>
            <th>DNI</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($personas)): foreach($personas as $p): 
              $colors = ['#8e44ad','#2980b9','#16a085','#e67e22','#c0392b'];
              $color = $colors[$p['idpersona'] % count($colors)];
          ?>
            <tr>
              <td><?= esc($p['idpersona']) ?></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="me-3">
                    <div class="person-avatar" style="background:<?= $color ?>">
                      <?= strtoupper(substr($p['nombres'],0,1) . (isset($p['apellidos'][0])?substr($p['apellidos'],0,1):'')) ?>
                    </div>
                  </div>
                  <div>
                    <div class="fw-bold"><?= esc($p['nombres']) . ' ' . esc($p['apellidos']) ?></div>
                    <div class="small-muted"><?= esc($p['direccion'] ?? '') ?></div>
                  </div>
                </div>
              </td>
              <td><?= esc($p['dni']) ?></td>
              <td><a href="tel:<?= esc($p['telefono']) ?>"><?= esc($p['telefono']) ?></a></td>
              <td><a href="mailto:<?= esc($p['correo']) ?>"><?= esc($p['correo']) ?></a></td>
              <td class="d-flex gap-1">
                <a href="<?= site_url('personas/editar/'.$p['idpersona']) ?>" class="btn btn-sm btn-outline-warning">Editar</a>
                <a href="<?= site_url('personas/eliminar/'.$p['idpersona']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar persona?')">Eliminar</a>
                <a href="javascript:void(0)" 
                   class="btn btn-sm btn-success btn-convertir-lead" 
                   data-id="<?= $p['idpersona'] ?>">
                   <i class="bi bi-arrow-right-circle"></i> Lead
                </a>
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

<!-- Modal para Lead -->
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <link rel="stylesheet" href="<?= base_url('css/.css') ?>">
    <div class="modal-content" id="leadModalContent">
      <!-- Se carga dinámicamente -->
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.btn-convertir-lead').forEach(btn => {
    btn.addEventListener('click', function(){
        const idpersona = this.dataset.id;
        fetch(`<?= site_url('leads/modals/') ?>${idpersona}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('leadModalContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('leadModal')).show();
            })
            .catch(err => console.error(err));
    });
});
</script>

<?= $footer ?>