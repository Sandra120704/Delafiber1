$(function () {

    // ==========================
    // Helper para armar URL
    // ==========================
    function u(path) {
        return (base_url.endsWith('/') ? base_url : base_url + '/') + path.replace(/^\/+/, '');
    }

    // ==========================
    // Inicializar eventos de tarjetas Kanban
    // ==========================
    function initKanbanCards() {

        // Click en tarjeta -> abrir modal detalle
        $(document).off("click", ".kanban-card").on("click", ".kanban-card", function () {
            const idlead = $(this).data("id");
            if (!idlead) return;

            $.get(u("lead/detalle/" + idlead))
                .done(function (html) {
                    $("#modalContainer").html(html);
                    const modalEl = document.getElementById("modalLeadDetalle");
                    if (modalEl) {
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();

                        // ==========================
                        // Botón Desistir
                        // ==========================
                        $("#btnDesistirLead").off("click").on("click", function () {
                            Swal.fire({
                                title: '¿Desea desistir este lead?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, desistir',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.post(u("lead/desistir"), { idlead })
                                        .done(res => {
                                            if(res.success) {
                                                Swal.fire('Desistido!', res.message, 'success');

                                                // Cerrar modal
                                                modal.hide();

                                                // Eliminar tarjeta del Kanban
                                                $("#kanban-card-" + idlead).remove();
                                            } else {
                                                Swal.fire('Error', res.message, 'error');
                                            }
                                        })
                                        .fail(() => Swal.fire('Error', 'No se pudo procesar la acción', 'error'));
                                }
                            });
                        });
                    }
                })
                .fail(function (xhr, status, error) {
                    console.error("Error AJAX al cargar detalle:", error, xhr.responseText);
                    Swal.fire('Error', 'No se pudo cargar el detalle del Lead', 'error');
                });
        });

        // ==========================
        // Drag & Drop
        // ==========================
        $(".kanban-card").attr("draggable", true);

        $(".kanban-column").each(function () {
            this.addEventListener('dragover', e => e.preventDefault());

            this.addEventListener('drop', function (e) {
                e.preventDefault();
                const cardId = e.dataTransfer.getData("text");
                const card = document.getElementById(cardId);
                if (!card) return;

                this.appendChild(card); // Mueve la tarjeta
                const idlead = card.dataset.id;
                const newEtapa = this.dataset.etapa;

                $.post(u('lead/actualizarEtapa'), { idlead, idetapa: newEtapa })
                    .done(res => {
                        if (!res.success) Swal.fire('Error', res.message, 'error');
                    })
                    .fail(() => Swal.fire('Error', 'No se pudo actualizar la etapa', 'error'));
            });
        });

        $(".kanban-card").on("dragstart", function (e) {
            e.originalEvent.dataTransfer.setData("text", this.id);
        });

    }

    // Inicializar al cargar
    initKanbanCards();

    // ==========================
    // Buscar persona por DNI
    // ==========================
    $("#btnBuscarDni").on("click", function () {
        let dni = $("#dni").val().trim();
        if (dni.length < 8) { 
            Swal.fire('Atención', 'Ingrese un DNI válido', 'warning'); 
            return; 
        }

        $.get(u("api/personas/buscardni/" + dni), function (data) {
            if (data.success) {
                $("#nombres").val(data.nombres);
                $("#apellidos").val(data.apepaterno + " " + data.apematerno);
                $("#telefono").val(data.telefono || "");
                $("#correo").val(data.correo || "");
            } else {
                Swal.fire('Atención', 'No se encontró persona, complete los datos manualmente', 'info');
            }
        }, "json");
    });

    // ==========================
    // Guardar Lead
    // ==========================
    $(document).on("submit", "#leadForm", function (e) {
        e.preventDefault();

        const idpersona = $("#idpersona").val();

        // Verificar si ya existe lead
        $.get(u("lead/verificar-duplicado/" + idpersona))
            .done(res => {
                if(res.exists) {
                    Swal.fire('Atención', 'Esta persona ya tiene un lead registrado', 'warning');
                    return;
                }

                // Registrar
                $.post($(this).attr("action"), $(this).serialize(), function (res) {
                    if (res.success) {
                        Swal.fire('¡Listo!', 'Lead registrado correctamente', 'success');
                        $("#leadModal").modal("hide");

                        // Crear tarjeta Kanban
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

                        // Re-inicializar eventos
                        initKanbanCards();

                    } else {
                        Swal.fire('Error', res.message || "No se pudo registrar el lead", 'error');
                    }
                }, "json");

            })
            .fail(() => Swal.fire('Error', 'No se pudo verificar la persona', 'error'));
    });

});
