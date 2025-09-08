<?= $header ?>

<link rel="stylesheet" href="<?= base_url('css/leads.css') ?>">

<div class="kanban-container">
    <!-- Cabecera con etapas -->
    <div class="kanban-header">
        <?php foreach ($etapas as $etapa): ?>
            <div class="kanban-stage">
                <?= htmlspecialchars($etapa['nombre']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Cuerpo con columnas y tarjetas -->
    <div class="kanban-body">
        <?php foreach ($etapas as $etapa): ?>
            <?php
            // Si no hay leads para esta etapa, la saltamos
            if (empty($leadsPorEtapa[$etapa['idetapa']])) continue;

            $leadsEtapa = $leadsPorEtapa[$etapa['idetapa']];
            ?>
            <div class="kanban-column"
                 id="kanban-column-<?= $etapa['idetapa'] ?>"
                 data-etapa="<?= $etapa['idetapa'] ?>">

                <?php foreach ($leadsEtapa as $lead): ?>
                    <div class="kanban-card"
                         id="kanban-card-<?= $lead['idlead'] ?>"
                         data-id="<?= $lead['idlead'] ?>"
                         style="border-left: 5px solid <?= htmlspecialchars($lead['estatus_color'] ?? '#007bff') ?>;"
                         draggable="true">

                        <div class="card-title">
                            <?= htmlspecialchars($lead['nombres'] . ' ' . $lead['apellidos']) ?>
                        </div>
                        <div class="card-info">
                            <?= htmlspecialchars($lead['telefono']) ?><br>
                            <?= htmlspecialchars($lead['correo']) ?><br>
                            <?= htmlspecialchars($lead['campana'] ?? '') ?> - <?= htmlspecialchars($lead['medio'] ?? '') ?><br>
                            Usuario: <?= htmlspecialchars($lead['usuario']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Contenedor para modales dinámicos -->
<div id="modalContainer"></div>

<?= $footer ?>

<script>
    const base_url = "<?= base_url() ?>";
</script>
<script src="<?= base_url('js/leads.js') ?>"></script>
