document.addEventListener('DOMContentLoaded', function() {

    const origenSelect = document.getElementById('origenSelect');
    const campaniaDiv = document.getElementById('campaniaDiv');
    const campSelect = document.getElementById('campaniaSelect');
    const referenteDiv = document.getElementById('referenteDiv');
    const referidoInput = document.getElementById('referido_por');
    const form = document.getElementById('leadForm');

    function actualizarDivs() {
        if (!origenSelect) return;
        const tipo = origenSelect.options[origenSelect.selectedIndex].getAttribute('data-tipo') || '';

        if(tipo === 'campaña') {
            campaniaDiv.style.display = 'block';
            referenteDiv.style.display = 'none';
            campSelect.required = true;
            referidoInput.required = false;
        } else if(tipo === 'referido') {
            campaniaDiv.style.display = 'none';
            referenteDiv.style.display = 'block';
            campSelect.required = false;
            referidoInput.required = true;
        } else {
            campaniaDiv.style.display = 'none';
            referenteDiv.style.display = 'none';
            campSelect.required = false;
            referidoInput.required = false;
        }
    }

    actualizarDivs();
    origenSelect.addEventListener('change', actualizarDivs);

    form.addEventListener('submit', function(e){
        e.preventDefault();
        const idpersona = document.getElementById('idpersona').value;

        if(!idpersona){
            // Persona no existe → crear primero
            $.post("<?= base_url('api/personas/crear') ?>", {
                nombres: document.getElementById('nombres').value,
                apellidos: document.getElementById('apellidos').value,
                telefono: document.getElementById('telefono').value,
                correo: document.getElementById('correo').value
            }, function(res){
                if(res.success){
                    document.getElementById('idpersona').value = res.idpersona;
                    registrarLead(res.idpersona);
                } else {
                    mostrarToast('error', res.message);
                }
            }, "json");
        } else {
            // Persona existe → validar si ya es Lead
            $.getJSON("<?= base_url('lead/validar') ?>/" + idpersona, function(res){
                if(res.exists){
                    mostrarToast('warning', 'Esta persona ya está registrada como Lead.');
                } else {
                    registrarLead(idpersona);
                }
            });
        }
    });

    function registrarLead(idpersona){
        $.post("<?= base_url('lead/guardar') ?>", $(form).serialize(), function(res){
            if(res.status === 'success'){
                // Cerrar modal primero
                const modalEl = document.getElementById('leadModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if(modal) modal.hide();

                // Crear tarjeta del lead
                const leadCard = `
                    <div class="kanban-card"
                         id="kanban-card-${res.idlead}"
                         data-id="${res.idlead}"
                         draggable="true"
                         style="border-left: 5px solid #007bff; margin-bottom: 10px; padding: 8px; background: #fff; border-radius: 6px;">
                        <div class="card-title">${document.getElementById('nombres').value} ${document.getElementById('apellidos').value}</div>
                        <div class="card-info">
                            ${document.getElementById('telefono').value}<br>
                            ${document.getElementById('correo').value}
                        </div>
                    </div>`;
                const column = document.getElementById('kanban-column-1');
                if(column) column.insertAdjacentHTML('beforeend', leadCard);

                mostrarToast('success', 'Lead registrado correctamente');

                // Limpiar formulario
                form.reset();
                document.getElementById('idpersona').value = '';

                if(typeof initKanbanCards === 'function') initKanbanCards();

            } else {
                mostrarToast('error', res.message);
            }
        }, "json");
    }

    function mostrarToast(icon, message){
        Swal.fire({
            icon: icon,
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    }

});
