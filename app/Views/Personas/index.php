<?= $header ?>

<h4>Registro de personas</h4>

<table class="table table-sm">
  <colgroup>
    <col width="10%">
    <col width="25%">
    <col width="25%">
    <col width="20%">
    <col width="20%">
  </colgroup>
  <thead>
    <tr>
      <th>ID</th>
      <th>Apellidos</th>
      <th>Nombres</th>
      <th>Teléfono</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>Pachas Contreras</td>
      <td>Hugo</td>
      <td>956111444</td>
      <td>
        <a href="#" class="btn btn-sm btn-info">Editar</a>
        <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
      </td>
    </tr>
    <tr>
      <td>2</td>
      <td>Sotelo Mendoz</td>
      <td>mónica</td>
      <td>956000444</td>
      <td>
        <a href="#" class="btn btn-sm btn-info">Editar</a>
        <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
      </td>
    </tr>
    <tr>
      <td>3</td>
      <td>Quintana Sandobal</td>
      <td>Martín</td>
      <td>956111555</td>
      <td>
        <a href="#" class="btn btn-sm btn-info">Editar</a>
        <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
      </td>
    </tr>
  </tbody>
</table>

<?= $footer ?>