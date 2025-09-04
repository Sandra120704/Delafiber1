$(function () {
    const base_url = "<?= base_url() ?>";

    // ==========================
    // Función auxiliar para URLs
    // ==========================
    function u(path) {
        return (base_url.endsWith('/') ? base_url : base_url + '/') + path.replace(/^\/+/, '');
    }

    // ==========================
    // Inicializar eventos de tarjetas
    // ==========================
    function initKanbanCards() {

        // Abrir modal detalle
        $(document).off('click', '.kanban-card').on('click', '.kanban-card', function() {
            const idlead = $(this).data('id');
            if(!idlead) return;

            $.get(u('lead/detalle/' + idlead))
                .done(function(html) {
                    $('#modalContainer').html(html);
                    const modalEl = document.getElementById('modalLeadDetalle');
                    if(modalEl){
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                })
                .fail(function(xhr) {
                    console.error('Error AJAX al cargar detalle:', xhr.status, xhr.statusText);
                    alert('No se pudo cargar el detalle del Lead');
                });
        });

        // Hacer tarjetas arrastrables
        $('.kanban-card').attr('draggable', true);
    }

    initKanbanCards();

    // ==========================
    // Drag & Drop Kanban
    // ==========================
    let draggedCard = null;

    $(document).on('dragstart', '.kanban-card', function() {
        draggedCard = this;
        $(this).addClass('dragging');
    });

    $(document).on('dragend', '.kanban-card', function() {
        $(this).removeClass('dragging');
        draggedCard = null;
    });

    $(document).on('dragover', '.kanban-column', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    $(document).on('dragleave', '.kanban-column', function() {
        $(this).removeClass('dragover');
    });

    $(document).on('drop', '.kanban-column', function() {
        $(this).removeClass('dragover');
        if (!draggedCard) return;

        const newEtapa = $(this).data('etapa');
        const idlead = $(draggedCard).data('id');

        $(this).append(draggedCard);

        $.post(u('lead/avanzarEtapa'), { idlead, idetapa: newEtapa }, function(res) {
            if(!res.success){
                alert('Error al mover Lead');
            }
        }, 'json');
    });

    // ==========================
    // Convertir persona a Lead
    // ==========================
    $(document).on('click', '.btn-convertir-lead', function() {
        const idpersona = $(this).data('idpersona');
        if(!idpersona) return;

        $.post(u('lead/convertir'), { idpersona }, function(res) {
            if(res.success){
                // Agregar tarjeta a la columna correspondiente
                const columna = $('#kanban-column-' + res.idetapa);
                if(columna.length){
                    columna.append(res.html_card);
                    initKanbanCards();
                }
            } else {
                alert('Error al convertir persona a Lead');
            }
        }, 'json');
    });

    // ==========================
    // Desistir / Eliminar Lead
    // ==========================
    $(document).on('click', '.btn-desistir', function() {
        const idlead = $(this).data('idlead');
        if(!idlead) return;

        if(!confirm('¿Desea desistir / eliminar este Lead?')) return;

        $.post(u('lead/eliminar'), { idlead }, function(res) {
            if(res.success){
                $('#kanban-card-' + idlead).fadeOut(400, function(){ $(this).remove(); });
            } else {
                alert('Error al desistir Lead');
            }
        }, 'json');
    });

});
