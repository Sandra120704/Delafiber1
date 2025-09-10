<?= $header ?>

<style>
.main-card {
    max-width: 12000rem;
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(12, 38, 63, 0.08);
    background: #fff;
}

.person-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    text-transform: uppercase;
}

.table-hover tbody tr:hover {
    background: #f8f9fa;
}

.small-muted {
    font-size: 0.85rem;
    color: #6c757d;
}

.btn-group-actions > * {
    margin-right: 0.25rem;
}
</style>

<div class="d-flex justify-content-center py-5">
  <div class="main-card p-4 mx-auto">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3 class="mb-0">Personas</h3>
        <div class="small-muted">Listado de contactos registrados</div>
      </div>
      <a href="<?= site_url('personas/crear') ?>" class="btn btn-primary">+ Crear persona</a>
    </div>

    <form class="mb-3" method="get" action="<?= site_url('personas') ?>">
      <div class="input-group">
        <input name="q" value="<?= esc($_GET['q'] ?? '') ?>" class="form-control" placeholder="Buscar por nombre, DNI, teléfono o correo" autocomplete="off">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-sm table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Contacto</th>
            <th>DNI</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($personas)) : 
            $colors = ['#8e44ad','#2980b9','#16a085','#e67e22','#c0392b'];
            foreach ($personas as $p):
              $color = $colors[$p['idpersona'] % count($colors)];
          ?>
            <tr>
              <td><?= esc($p['idpersona']) ?></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="me-3">
                    <div class="person-avatar" style="background:<?= $color ?>;">
                      <?= strtoupper(substr($p['nombres'], 0, 1) . (isset($p['apellidos'][0]) ? substr($p['apellidos'], 0, 1) : '')) ?>
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
              <td class="text-center">
                <div class="btn-group btn-group-actions" role="group" aria-label="Acciones">
                  <button type="button" class="btn btn-sm btn-outline-warning btn-editar" data-id="<?= $p['idpersona'] ?>">Editar</button>
                  <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar" data-id="<?= $p['idpersona'] ?>">Eliminar</button>
                  <button type="button" class="btn btn-sm btn-success btn-convertir-lead" data-id="<?= $p['idpersona'] ?>" title="Convertir en Lead">
                    <i class="bi bi-arrow-right-circle"></i> Lead
                  </button>
                </div>
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

<div id="modalContainer"></div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará la persona permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e74c3c',
      }).then(result => {
        if (result.isConfirmed) {
          window.location.href = `<?= base_url('personas/eliminar/') ?>${id}`;
        }
      });
    });
  });

  document.querySelectorAll('.btn-convertir-lead').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;

      fetch(`<?= base_url('leads/modals') ?>/${id}`)
        .then(res => res.text())
        .then(html => {
          const container = document.getElementById('modalContainer');
          container.innerHTML = html;

          // Ejecutar lógica para campos (si el script está fuera del modal)
          initModalCampos();

          const modalEl = document.getElementById('leadModal');
          if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Opcional: limpiar modal al cerrarlo
            modalEl.addEventListener('hidden.bs.modal', () => {
              container.innerHTML = '';
            });
          }
        })
        .catch(() => {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo conectar con el servidor',
          });
        });
    });
  });

  function initModalCampos() {
    const origenSelect = document.getElementById('origenSelect');
    const campaniaDiv = document.getElementById('campaniaDiv');
    const referenteDiv = document.getElementById('referenteDiv');

    if (!origenSelect) return;

    function actualizarDivs() {
      const selectedOption = origenSelect.options[origenSelect.selectedIndex];
      const tipo = selectedOption.getAttribute('data-tipo') || '';
      const campSelect = document.getElementById('campaniaSelect');
      const referidoInput = document.getElementById('referido_por');

      if (tipo === 'campania') {
        campaniaDiv.style.display = 'block';
        referenteDiv.style.display = 'none';
        campSelect.required = true;
        referidoInput.required = false;
      } else if (tipo === 'referido') {
        campaniaDiv.style.display = 'none';
        referenteDiv.style.display = 'block';
        campSelect.required = false;
        referidoInput.required = true;
      } else {
        campaniaDiv.style.display = 'none';
        referenteDiv.style.display = 'none';
        campSelect.required = false;
        referidoInput.required = false;
      }
    }

    origenSelect.addEventListener('change', actualizarDivs);
    actualizarDivs();
  }


    document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      fetch(`<?= site_url('personas/editar/') ?>${id}`)
        .then(res => res.text())
        .then(html => {
          document.getElementById('modalContainer').innerHTML = html;

          const modalEl = document.getElementById('editModal'); // Debes poner id="editModal" en el modal de editar
          if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
          }
        })
        .catch(err => {
          console.error('Error cargando el formulario de edición:', err);
        });
    });
  });
});
</script>

<?= $footer ?>
