$(function () {
  const base_url = "http://delafiber.test/";

  function u(path) {
    return (base_url.endsWith('/') ? base_url : base_url + '/') + path.replace(/^\/+/, '');
  }

  $(document).on("submit","#leadForm", function(e){
    e.preventDefault();
    let idpersona = $("#idpersona").val();

    if(!idpersona){
      // Crear persona primero
      $.post(u("api/personas/crear"), {
        nombres: $("#nombres").val(),
        apellidos: $("#apellidos").val(),
        telefono: $("#telefono").val(),
        correo: $("#correo").val()
      }, function(res){
        if(res.success){
          $("#idpersona").val(res.idpersona);
          registrarLead(res.idpersona);
        } else {
          Swal.fire('Error',res.message,'error');
        }
      }, "json");
    } else {
      registrarLead(idpersona);
    }
  });

  function registrarLead(idpersona){
    $.post(u("lead/guardar"), $("#leadForm").serialize(), function(res){
      if(res.success){
        Swal.fire('¡Listo!','Lead registrado correctamente','success');
        $("#leadModal").modal("hide");

        const leadCard = `
          <div class="kanban-card"
               id="kanban-card-${res.idlead}"
               data-id="${res.idlead}"
               draggable="true"
               style="border-left: 5px solid #007bff;">
            <div class="card-title">${res.persona.nombres} ${res.persona.apellidos}</div>
            <div class="card-info">
              ${res.persona.telefono ?? ""}<br>
              ${res.persona.correo ?? ""}
            </div>
          </div>`;
        $("#kanban-column-" + res.idetapa).append(leadCard);
      } else {
        Swal.fire('Error', res.message,'error');
      }
    }, "json");
  }
});
