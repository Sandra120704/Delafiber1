$(function () {

    // ==========================
    // Helper para armar URL
    // ==========================
    function u(path) {
        return (base_url.endsWith('/') ? base_url : base_url + '/') + path.replace(/^\/+/, '');
    }

    // ==========================
    // Buscar persona por DNI
    // ==========================
    $("#btnBuscarDni").on("click", function () {
        let dni = $("#dni").val().trim();
        if (dni.length < 8) {
            alert("Ingrese un DNI válido (8 dígitos)");
            return;
        }

        $.get(u("api/personas/buscardni/" + dni), function (data) {
            if (data.success) {
                $("#nombres").val(data.nombres);
                $("#apellidos").val(data.apepaterno + " " + data.apematerno);
                $("#telefono").val(data.telefono || "");
                $("#correo").val(data.correo || "");
            } else {
                alert("No se encontró persona, complete los datos manualmente.");
            }
        }, "json");
    });

    // ==========================
    // Guardar Lead
    // ==========================
    $(document).on("submit", "#leadForm", function (e) {
        e.preventDefault();

        $.post($(this).attr("action"), $(this).serialize(), function (res) {
            if (res.success) {
                alert("✅ Lead registrado correctamente");
                $("#leadModal").modal("hide");

                // Agregar tarjeta al Kanban (sin recargar toda la página)
                const leadCard = `
                    <div class="kanban-card"
                         id="kanban-card-${res.idlead}"
                         data-id="${res.idlead}"
                         draggable="true"
                         style="border-left: 5px solid #007bff;">
                        <div class="card-title">
                            ${res.persona.nombres} ${res.persona.apellidos}
                        </div>
                        <div class="card-info">
                            ${res.persona.telefono ?? ""}<br>
                            ${res.persona.correo ?? ""}
                        </div>
                    </div>`;

                // Insertar en la columna de la etapa inicial
                $("#kanban-column-" + res.idetapa).append(leadCard);

                // Re-inicializar eventos
                initKanbanCards();

            } else {
                alert(res.message || "Error al registrar Lead");
            }
        }, "json");
    });

    // ==========================
    // Inicializar eventos de tarjetas (detalle / drag)
    // ==========================
    function initKanbanCards() {
        // Abrir modal detalle
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
                    }
                })
                .fail(function () {
                    alert("Error al cargar detalle del Lead");
                });
        });

        // Hacer arrastrables
        $(".kanban-card").attr("draggable", true);
    }

    // Llamar al iniciar
    initKanbanCards();
});
