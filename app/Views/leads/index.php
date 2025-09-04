<?php require_once 'header.php'; ?>

<div class="container mt-4">
    <h2>Flujo de Trabajo - Leads</h2>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCrearLead">
        + Nuevo Lead
    </button>

    <?php foreach($pipelines as $pipeline): ?>
        <div class="mb-5">
            <h4><?= $pipeline->nombre ?></h4>
            <div class="row">
                <?php 
                $etapasPipeline = array_filter($etapas, fn($e) => $e->idpipeline == $pipeline->idpipeline);
                foreach($etapasPipeline as $etapa): 
                ?>
                    <div class="col-md-3">
                        <div class="card border-secondary mb-3">
                            <div class="card-header bg-light text-center">
                                <?= $etapa->nombre ?>
                            </div>
                            <div class="card-body" style="min-height:150px;">
                                <?php 
                                $leadsEtapa = array_filter($leads, fn($l) => $l->idetapa == $etapa->idetapa);
                                foreach($leadsEtapa as $lead): 
                                ?>
                                    <div class="card mb-2 p-2 lead-card" 
                                         style="cursor:pointer; background-color: <?= $lead->estatus_color ?>;"
                                         data-idlead="<?= $lead->idlead ?>">
                                        <strong><?= $lead->nombre_persona ?></strong><br>
                                        <?= $lead->telefono ?><br>
                                        <?= $lead->email ?><br>
                                        <?= $lead->campaña ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'leads/crear.php'; ?>
<?php require_once 'footer.php'; ?>

<script src="<?= base_url('js/leads.js') ?>"></script>
