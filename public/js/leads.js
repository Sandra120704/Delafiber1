$(function () {

    const base_url = "http://delafiber.test/";

    function u(path) {
        return (base_url.endsWith('/') ? base_url : base_url + '/') + path.replace(/^\/+/, '');
    }

    function initKanbanCards() {
        $(document).off("click", ".kanban-card").on("click", ".kanban-card", function () {
            const idlead = $(this).data("id");
            console.log("ID Lead:", idlead);
            if (!idlead) return;

            $.get(u("lead/detalle/" + idlead))
            .done(function(res) {
                console.log("Respuesta detalle lead:", res);
                // Cargar modal
                $("#modalContainer").html(res.html);
                const modalEl = document.getElementById("modalLeadDetalle");
                if(modalEl){
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    $("#listaTareas").empty();
                    res.tareas.forEach(t => {
                        $("#listaTareas").append(`<li>${t.descripcion} - ${t.fecha_registro}</li>`);
                    });


                    $("#listaSeguimientos").empty();
                    res.seguimientos.forEach(s => {
                        // CORRECCIÓN: usar fecha_registro
                        $("#listaSeguimientos").append(`<li>${s.comentario} - ${s.fecha_registro}</li>`);
                    });

                    $("#tareaForm").off("submit").on("submit", function(e){
                        e.preventDefault();
                        const descripcion = $(this).find('input[name="descripcion"]').val().trim();
                        const idlead = $("#tareaIdLead").val();
                        if(!descripcion) return;

                        $.post(u("lead/guardarTarea"), { idlead, descripcion })
                        .done(function(res){
                            if(res.success){
                                $("#listaTareas").append(`<li>${res.tarea.descripcion} - ${res.tarea.fecha_registro}</li>`);
                                $("#tareaForm")[0].reset();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        })
                        .fail(()=> Swal.fire('Error','No se pudo registrar la tarea','error'));
                    });


                    $("#seguimientoForm").off("submit").on("submit", function(e){
                        e.preventDefault();
                        const comentario = $(this).find('textarea[name="comentario"]').val().trim();
                        const idmodalidad = $(this).find('select[name="idmodalidad"]').val();
                        const idlead = $("#tareaIdLead").val();
                        if(!comentario) return;

                        $.post(u("lead/guardarSeguimiento"), { idlead, idmodalidad, comentario })
                        .done(function(res){
                            if(res.success){
                                $("#listaSeguimientos").append(`<li>${res.seguimiento.comentario} - ${res.seguimiento.fecha_registro}</li>`);
                                $("#seguimientoForm")[0].reset();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        })
                        .fail(()=> Swal.fire('Error','No se pudo registrar el seguimiento','error'));
                    });

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
                                })
                                .fail(()=>Swal.fire('Error','No se pudo procesar la acción','error'));
                            }
                        });
                    });
                }
            })
            .fail(function() {
                Swal.fire('Error', 'No se pudo cargar el detalle del Lead', 'error');
            });
        });

        $(".kanban-card").attr("draggable", true);
        $(".kanban-column").each(function(){
            this.addEventListener('dragover', e => e.preventDefault());
            this.addEventListener('drop', function(e){
                e.preventDefault();
                const cardId = e.dataTransfer.getData("text");
                const card = document.getElementById(cardId);
                if(!card) return;
                this.appendChild(card);

                const idlead = card.dataset.id;
                const newEtapa = this.dataset.etapa;

                $.post(u('lead/actualizarEtapa'), { idlead, idetapa: newEtapa })
                .done(res => { if(!res.success) Swal.fire('Error', res.message, 'error'); })
                .fail(()=> Swal.fire('Error','No se pudo actualizar la etapa','error'));
            });
        });
        $(".kanban-card").on("dragstart", function(e){
            e.originalEvent.dataTransfer.setData("text", this.id);
        });
    }

    initKanbanCards();

    $("#btnBuscarDni").on("click", function(){
        let dni = $("#dni").val().trim();
        if(dni.length < 8){ Swal.fire('Atención','Ingrese un DNI válido','warning'); return; }

        $.get(u("api/personas/buscardni/" + dni), function(data){
            if(data.success){
                $("#idpersona").val(data.idpersona);
                $("#nombres").val(data.nombres);
                $("#apellidos").val(data.apepaterno + ' ' + data.apematerno);
                $("#telefono").val(data.telefono || '');
                $("#correo").val(data.correo || '');
            } else {
                $("#idpersona").val('');
                Swal.fire('Info','No se encontró persona, ingrese datos manualmente','info');
            }
        }, "json");
    });

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
                } else { Swal.fire('Error',res.message,'error'); }
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
                initKanbanCards();
            } else {
                Swal.fire('Error', res.message,'error');
            }
        }, "json");
    }

});
