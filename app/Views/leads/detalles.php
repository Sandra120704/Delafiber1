<div>
    <h5>Datos de la Persona</h5>
    <p><strong>Nombre:</strong> <?= $lead->nombre_persona ?></p>
    <p><strong>Teléfono:</strong> <?= $lead->telefono ?></p>
    <p><strong>Email:</strong> <?= $lead->email ?></p>
    <p><strong>Difusión:</strong> <?= $lead->campaña ?></p>

    <h5>Historial de Seguimientos</h5>
    <ul>
        <?php foreach($seguimientos as $s): ?>
            <li><?= $s->fecha ?> - <?= $s->modalidad ?> - <?= $s->resultado_contacto ?></li>
        <?php endforeach; ?>
    </ul>

    <h5>Tareas</h5>
    <ul>
        <?php foreach($tareas as $t): ?>
            <li><?= $t->descripcion ?> (Responsable: <?= $t->usuario ?>)</li>
        <?php endforeach; ?>
    </ul>

    <button class="btn btn-primary" id="btnAvanzarEtapa" data-idlead="<?= $lead->idlead ?>">Avanzar Etapa</button>
    <button class="btn btn-secondary" id="btnRegistrarSeguimiento" data-idlead="<?= $lead->idlead ?>">Registrar Seguimiento</button>
</div>
