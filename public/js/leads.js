$(function () {
    // Construir URLs
    function u(path) {
        return (base_url.endsWith('/') ? base_url : base_url + '/') + path.replace(/^\/+/, '');
    }

    // ======== Abrir Modal de Detalle de Lead ========
    $(function () {
    $(document).on('click', '.kanban-card', function() {
        const idlead = $(this).data('id');
        if (!idlead) {
            console.error('ID del lead no definido para esta tarjeta', this);
            return;
        }

        $.get(base_url + 'lead/detalle/' + idlead)
            .done(function(html) {
                $('#modalContainer').html(html); // inyecta el modal
                const modalEl = document.getElementById('modalLeadDetalle');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                } else {
                    console.error('No se encontró el modal en el HTML recibido');
                }
            })
            .fail(function(xhr, status, error) {
                console.error('Error al cargar detalle del lead:', status, error);
                alert('No se pudo cargar el detalle del Lead');
            });
    });
});


    // ======== Drag & Drop para Kanban (cambio de etapa) ========
    $(".kanban-card").attr('draggable', true);

    let draggedCard = null;

    $(document).on('dragstart', '.kanban-card', function(e) {
        draggedCard = this;
        $(this).addClass('dragging');
    });

    $(document).on('dragend', '.kanban-card', function(e) {
        $(this).removeClass('dragging');
        draggedCard = null;
    });

    $(document).on('dragover', '.kanban-column', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    $(document).on('dragleave', '.kanban-column', function(e) {
        $(this).removeClass('dragover');
    });

    $(document).on('drop', '.kanban-column', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        if (!draggedCard) return;

        const newEtapa = $(this).data('etapa');
        const idlead = $(draggedCard).data('id');

        $(this).append(draggedCard); // Mover tarjeta visualmente

        // Guardar cambio de etapa vía AJAX
        $.post(u('lead/avanzarEtapa'), { idlead, idetapa: newEtapa }, function(res) {
            if(!res.success){
                alert('Error al mover Lead');
            }
        }, 'json');
    });
});
