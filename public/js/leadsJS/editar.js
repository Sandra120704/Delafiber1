$(document).ready(function(){

    window.abrirModalAgregarLead = function(idetapa){
        $('#idetapa').val(idetapa);
        const modal = new bootstrap.Modal(document.getElementById('leadModal'));
        modal.show();
    }

    $('#leadForm').on('submit', function(e){
        e.preventDefault();
        $.post(`${base_url}/lead/guardar`, $(this).serialize(), function(res){
            if(res.success){
                const column = $('#kanban-column-' + $('#idetapa').val());
                const leadCard = $(`
                    <div class="kanban-card" id="kanban-card-${res.idlead}" data-id="${res.idlead}" draggable="true" style="border-left:5px solid #007bff;">
                        <div class="card-title">${$('#nombres').val()} ${$('#apellidos').val()}</div>
                        <div class="card-info">
                            <small>${$('#telefono').val()} | ${$('#correo').val()}</small>
                        </div>
                    </div>
                `);
                column.append(leadCard);
                enableDragAndDrop(); // reactiva drag & drop
                $('#leadForm')[0].reset();
                const modal = bootstrap.Modal.getInstance(document.getElementById('leadModal'));
                modal.hide();
                Swal.fire({ icon:'success', title:'Lead registrado', toast:true, position:'top-end', showConfirmButton:false, timer:2000, timerProgressBar:true });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, "json");
    });

});
