<?= $header ?>
<!-- Hoja de estilos de Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Estilos personalizados para el tablero -->
<link rel="stylesheet" href="<?= base_url('css/leads.css') ?>">
<!-- Estilos para SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">
<!-- Tailwind CSS (se puede incluir directamente si no está en el proyecto) -->
<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-gray-100 min-h-screen font-sans antialiased">
    <!-- Cabecera del tablero -->
    <div class="flex justify-between items-center mb-8">
        <h3 class="text-3xl font-bold text-gray-800">Flujo de Leads</h3>
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-xl transition duration-300 transform hover:scale-105" data-bs-toggle="modal" data-bs-target="#leadModal">
            Nuevo Lead
        </button>
    </div>

    <!-- Contenedor del Kanban -->
    <div class="kanban-container grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php foreach ($etapas as $etapa): ?>
        <div class="kanban-column bg-white rounded-xl shadow-lg p-6" id="kanban-column-<?= $etapa['idetapa'] ?>" data-etapa="<?= $etapa['idetapa'] ?>">
            <div class="kanban-stage text-center font-semibold text-lg mb-4 text-gray-700"><?= htmlspecialchars($etapa['nombre']) ?></div>
            <?php
              $leadsEtapa = $leadsPorEtapa[$etapa['idetapa']] ?? [];
            ?>
            <?php foreach ($leadsEtapa as $lead): ?>
              <div class="kanban-card bg-white rounded-lg shadow-md mb-4 p-4 cursor-pointer transition-transform transform hover:scale-105 hover:shadow-xl" 
                    id="kanban-card-<?= $lead['idlead'] ?>" 
                    data-id="<?= $lead['idlead'] ?>" 
                    draggable="true" 
                    style="border-left:5px solid <?= htmlspecialchars($lead['estatus_color'] ?? '#007bff') ?>;">
                <div class="card-title text-sm font-bold text-gray-900 mb-1"><?= htmlspecialchars($lead['nombres'].' '.$lead['apellidos']) ?></div>
                <div class="card-info text-xs text-gray-500">
                  <small class="block truncate"><?= htmlspecialchars($lead['telefono']) ?> | <?= htmlspecialchars($lead['correo']) ?></small>
                  <small class="block truncate"><?= htmlspecialchars($lead['campania'] ?? '') ?> - <?= htmlspecialchars($lead['medio'] ?? '') ?></small>
                  <small class="block truncate">Usuario: <?= htmlspecialchars($lead['usuario']) ?></small>
                </div>
              </div>
            <?php endforeach; ?>
            <button class="btn btn-sm btn-outline-primary w-full mt-2 text-blue-500 hover:bg-blue-500 hover:text-white transition duration-300 rounded-lg border border-blue-500 py-2">
                + Agregar Lead
            </button>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Modal de Detalle (el contenido se carga con JS) -->
    <div class="modal fade" id="modalLeadDetalle" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-xl shadow-2xl"></div>
      </div>
    </div>

    <!-- Modal de Creación de Lead -->
    <div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-xl shadow-2xl border-t-4 border-blue-500">
          <div class="modal-header border-b border-gray-200 p-4 flex justify-between items-center">
            <h5 class="modal-title text-xl font-bold text-gray-800">Registrar Lead</h5>
            <button type="button" class="btn-close text-gray-400 hover:text-gray-600 transition-colors" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-6">
            <form id="leadForm">
              <input type="hidden" id="idpersona" name="idpersona" value="">
              <input type="hidden" id="idetapa" name="idetapa" value="1">
    
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">DNI:</label>
                <input type="text" id="dni" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="DNI">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nombres:</label>
                <input type="text" id="nombres" name="nombres" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Correo:</label>
                <input type="email" id="correo" name="correo" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>
              
              <div class="modal-footer border-t border-gray-200 p-4 flex justify-end gap-2">
                <button type="button" class="btn btn-secondary rounded-md shadow-sm px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success rounded-md shadow-sm px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">Registrar Lead</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>

<?= $footer ?>

<script>
    const base_url = "<?= rtrim(base_url(), '/') ?>";
</script>
<!-- Scripts JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('js/leadsJS/kanbas.js') ?>"></script>
<script src="<?= base_url('js/leadsJS/editar.js') ?>"></script>
<script src="<?= base_url('js/leadsJS/detalle.js') ?>"></script>
<script type="module" src="<?= base_url('js/leadsJS/leadsForm.js') ?>"></script>
