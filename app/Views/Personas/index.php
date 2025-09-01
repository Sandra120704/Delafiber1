<?= $header ?>

<div class="container mt-4">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/persona.css') ?>">


<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Lista De Personas</h2>
  <a href="<?= base_url('persona/crear') ?>" class="btn btn-corporativo">➕ Agregar Persona</a>
</div>


<table class="table table-bordered table-hover table-morado">
  <thead>
      <tr>
        <th>ID</th>
        <th>Apellidos</th>
        <th>Nombres</th>
        <th>Distrito</th>
        <th>Provincia</th>
        <th>Departamento</th>
      </tr>
  </thead>
  <tbody>
      <?php if (!empty($personas)): ?>
        <?php foreach ($personas as $persona): ?>
          <tr>
            <td><?= $persona->idpersona ?></td>
            <td><?= $persona->apellidos ?></td>
            <td><?= $persona->nombres ?></td>
            <td><?= $persona->distrito ?></td>
            <td><?= $persona->provincias ?></td>
            <td><?= $persona->departamento ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" class="text-center">No hay personas registradas</td>
        </tr>
      <?php endif; ?>
  </tbody>
</table>
</div>

<?= $footer ?>