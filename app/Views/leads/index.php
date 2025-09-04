<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/leads.css') ?>">

<div class="kanban-container">
    <div class="kanban-header">
        <?php foreach ($etapas as $etapa): ?>
            <div class="kanban-stage"><?= htmlspecialchars($etapa->nombreetapa) ?></div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-body">
    <?php foreach ($etapas as $etapa): ?>
        <div class="kanban-column" data-etapa="<?= $etapa->idetapa ?>">
            <?php
            $leadsEtapa = array_filter($leads, fn($l) => $l->idetapa == $etapa->idetapa);
            foreach ($leadsEtapa as $lead):
            ?>
                <div class="kanban-card" 
                     data-id="<?= $lead->idpersona ?>" 
                     style="border-left: 5px solid <?= htmlspecialchars($lead->estatus_color) ?>;">
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

<!-- Contenedor para modales -->
<div id="modalContainer"></div>


<?= $footer ?>
<script src="<?= base_url('js/leads.js') ?>"></script>
