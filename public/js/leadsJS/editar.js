$(document).ready(function() {

    // -------------------------------
    // Función para abrir modal de nuevo Lead
    // -------------------------------
    window.abrirModalAgregarLead = function(idetapa){
        $('#idetapa').val(idetapa);
        const modal = new bootstrap.Modal(document.getElementById('leadModal'));
        modal.show();
    }

    // -------------------------------
    // Guardar Lead vía AJAX
    // -------------------------------
    $('#leadForm').on('submit', function(e){
        e.preventDefault();
        const formData = $(this).serialize();

        $.post(`${base_url}/leads/guardar`, formData, function(res){
            if(res.success){
                const persona = res.persona;
                const idetapa = persona.idetapa;

                // Buscar columna correspondiente
                const column = $('#kanban-column-' + idetapa);
                if(column.length === 0){
                    console.error('Columna Kanban no encontrada para idetapa:', idetapa);
                    return;
                }

                // Crear tarjeta nueva
                const leadCard = $(`
                    <div class="kanban-card" id="kanban-card-${res.idlead}" data-id="${res.idlead}" draggable="true" style="border-left:5px solid #007bff;">
                        <div class="card-title">${persona.nombres} ${persona.apellidos}</div>
                        <div class="card-info">
                            <small>${persona.telefono || ''} | ${persona.correo || ''}</small>
                        </div>
                    </div>
                `);

                column.append(leadCard);

                // Reactivar drag & drop
                if(typeof enableDragAndDrop === "function"){
                    enableDragAndDrop();
                }

                // Cerrar modal y limpiar formulario
                const modal = bootstrap.Modal.getInstance(document.getElementById('leadModal'));
                modal.hide();
                $('#leadForm')[0].reset();

                Swal.fire({
                    icon: 'success',
                    title: 'Lead registrado',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, "json");
    });

    // -------------------------------
    // Convertir persona a Lead vía AJAX
    // -------------------------------
    window.convertirPersonaALead = function(idpersona){
        $.post(`${base_url}/leads/convertirALead/${idpersona}`, {}, function(res){
            if(res.success){
                const persona = res.persona;
                const idetapa = persona.idetapa;

                const column = $('#kanban-column-' + idetapa);
                if(column.length === 0){
                    console.error('Columna Kanban no encontrada para idetapa:', idetapa);
                    return;
                }

                const leadCard = $(`
                    <div class="kanban-card" id="kanban-card-${res.idlead}" data-id="${res.idlead}" draggable="true" style="border-left:5px solid #007bff;">
                        <div class="card-title">${persona.nombres} ${persona.apellidos}</div>
                        <div class="card-info">
                            <small>${persona.telefono || ''} | ${persona.correo || ''}</small>
                        </div>
                    </div>
                `);

                column.append(leadCard);

                if(typeof enableDragAndDrop === "function"){
                    enableDragAndDrop();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Lead creado correctamente',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, "json");
    }

});
