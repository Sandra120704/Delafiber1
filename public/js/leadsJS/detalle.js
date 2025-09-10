$(function () {
  const base_url = "http://delafiber.test/";

  function u(path) {
    return (base_url.endsWith('/') ? base_url : base_url + '/') + path.replace(/^\/+/, '');
  }

  $(document).on("click", ".kanban-card", function () {
    const idlead = $(this).data("id");
    if (!idlead) return;

    $.get(u("lead/detalle/" + idlead))
      .done(function(res) {
        $("#modalContainer").html(res.html);
        const modalEl = document.getElementById("modalLeadDetalle");
        if(modalEl){
          const modal = new bootstrap.Modal(modalEl);
          modal.show();

          // Tareas
          $("#tareaForm").off("submit").on("submit", function(e){
            e.preventDefault();
            const descripcion = $(this).find('input[name="descripcion"]').val().trim();
            if(!descripcion) return;

            $.post(u("lead/guardarTarea"), { idlead, descripcion })
              .done(function(res){
                if(res.success){
                  $("#listaTareas").append(`<li>${res.tarea.descripcion} - ${res.tarea.fecha_registro}</li>`);
                  $("#tareaForm")[0].reset();
                } else {
                  Swal.fire('Error', res.message, 'error');
                }
              });
          });

          // Seguimientos
          $("#seguimientoForm").off("submit").on("submit", function(e){
            e.preventDefault();
            const comentario = $(this).find('textarea[name="comentario"]').val().trim();
            const idmodalidad = $(this).find('select[name="idmodalidad"]').val();
            if(!comentario) return;

            $.post(u("lead/guardarSeguimiento"), { idlead, idmodalidad, comentario })
              .done(function(res){
                if(res.success){
                  $("#listaSeguimientos").append(`<li>${res.seguimiento.comentario} - ${res.seguimiento.fecha_registro}</li>`);
                  $("#seguimientoForm")[0].reset();
                } else {
                  Swal.fire('Error', res.message, 'error');
                }
              });
          });

          // Desistir
          $("#btnDesistirLead").off("click").on("click", function(){
            Swal.fire({
              title:'¿Desea desistir este lead?',
              icon:'warning',
              showCancelButton:true,
              confirmButtonText:'Sí, desistir',
              cancelButtonText:'Cancelar'
            }).then(result=>{
              if(result.isConfirmed){
                $.post(u("lead/eliminar"), { idlead })
                  .done(res=>{
                    if(res.success){
                      Swal.fire('Desistido!', res.message, 'success');
                      modal.hide();
                      $("#kanban-card-" + idlead).remove();
                    } else {
                      Swal.fire('Error', res.message, 'error');
                    }
                  });
              }
            });
          });
        }
      });
  });
});
