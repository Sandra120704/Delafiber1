<?= $header ?>

<link rel="stylesheet" href="<?= base_url('css/leads.css') ?>">

<div class="kanban-container">

    <?php if(!empty($mensajeExito)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensajeExito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <!-- Cabecera con etapas -->
    <div class="kanban-header">
        <?php foreach ($etapas as $etapa): ?>
            <div class="kanban-stage"><?= htmlspecialchars($etapa->nombreetapa) ?></div>
        <?php endforeach; ?>
    </div>

    <!-- Cuerpo con columnas y tarjetas -->
    <div class="kanban-body">
        <?php foreach ($etapas as $etapa): ?>
            <div class="kanban-column" data-etapa="<?= $etapa->idetapa ?>">
                <?php
                $leadsEtapa = array_filter($leads, fn($l) => $l->idetapa == $etapa->idetapa);
                foreach ($leadsEtapa as $lead):
                ?>
                    <div class="kanban-card"
                         data-id="<?= $lead->idlead ?>"
                         style="border-left: 5px solid <?= htmlspecialchars($lead->estatus_color) ?>;"
                         draggable="true">
                        <div class="card-title"><?= htmlspecialchars($lead->nombres . ' ' . $lead->apellidos) ?></div>
                        <div class="card-info">
                            <?= htmlspecialchars($lead->telefono) ?><br>
                            <?= htmlspecialchars($lead->email) ?><br>
                            <?= htmlspecialchars($lead->campaña) ?> - <?= htmlspecialchars($lead->medio) ?>
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
