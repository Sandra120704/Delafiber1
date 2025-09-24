// Inicialización del módulo de campañas
$(document).ready(function() {
    console.log('🚀 Módulo de campañas inicializado');
    
    // Inicializar DataTables con configuración moderna
    const campaignsTable = $('#campaignsTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
        },
        pageLength: 10,
        order: [[1, 'asc']],
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        drawCallback: function() {
            // Reinicializar tooltips después de cada redibujado
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Actualizar contadores de registros mostrados
            const info = this.api().page.info();
            $('#showingFrom').text(info.start + 1);
            $('#showingTo').text(info.end);
            $('#totalEntries').text(info.recordsTotal);
        }
    });
    
    // Inicializar AOS para animaciones
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });
    
    // Tooltips y Popovers
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Gestión de filtros
    let activeFilters = new Set();
    
    function updateFilterTags() {
        const filterTagsContainer = $('#activeFilters');
        filterTagsContainer.empty();
        
        activeFilters.forEach(filter => {
            const tag = $(`
                <div class="badge bg-primary rounded-pill me-2 mb-2">
                    ${filter}
                    <button type="button" class="btn-close btn-close-white ms-2" data-filter="${filter}"></button>
                </div>
            `);
            filterTagsContainer.append(tag);
        });
    }
    
    // Limpiar filtros
    $('#clearFilters').on('click', function() {
        $('input.form-control').val('');
        $('select.form-select').val('');
        activeFilters.clear();
        updateFilterTags();
        campaignsTable.search('').columns().search('').draw();
    });
    
    // Acciones masivas
    let selectedRows = new Set();
    
    $('#selectAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
        if (isChecked) {
            $('.row-checkbox').each(function() {
                selectedRows.add($(this).val());
            });
        } else {
            selectedRows.clear();
        }
        updateBulkActionsState();
    });
    
    $(document).on('change', '.row-checkbox', function() {
        const value = $(this).val();
        if ($(this).prop('checked')) {
            selectedRows.add(value);
        } else {
            selectedRows.delete(value);
        }
        updateBulkActionsState();
    });
    
    function updateBulkActionsState() {
        const count = selectedRows.size;
        if (count > 0) {
            $('#bulkActions').removeClass('btn-light').addClass('btn-primary');
        } else {
            $('#bulkActions').removeClass('btn-primary').addClass('btn-light');
        }
    }
    
    // Botón de actualizar
    $('#refreshBtn').on('click', function() {
        const btn = $(this);
        btn.addClass('rotate-animation');
        setTimeout(() => btn.removeClass('rotate-animation'), 1000);
        location.reload();
    });
    
    // Ver detalle de campaña
    $(document).on('click', '.btn-detalle', function() {
        const idCampana = $(this).data('id');
        const modal = $('#detalleCampanaModal');
        
        // Simular carga
        modal.find('.modal-body').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
        modal.modal('show');
        
        // Cargar detalles vía AJAX
        $.get(`${BASE_URL}/campanas/detalle/${idCampana}`, function(response) {
            if (response.success) {
                const campana = response.data;
                
                // Actualizar información
                $('#detalleNombre').text(campana.nombre);
                $('#detalleDescripcion').text(campana.descripcion);
                $('#detalleFechas').html(`${campana.fecha_inicio} - ${campana.fecha_fin}`);
                $('#detallePresupuesto').text(campana.presupuesto);
                $('#detalleEstado').html(`
                    <span class="badge bg-${campana.estado === 'Activa' ? 'success' : 'secondary'}">
                        ${campana.estado}
                    </span>
                `);
                $('#detalleResponsable').text(campana.responsable_nombre);
                
                // Actualizar tabla de medios
                let mediosHTML = '';
                campana.medios.forEach(medio => {
                    mediosHTML += `
                        <tr>
                            <td>${medio.nombre}</td>
                            <td>S/ ${medio.inversion}</td>
                            <td>${medio.leads} leads</td>
                        </tr>
                    `;
                });
                $('#detalleMedios').html(mediosHTML);
            }
        });
    });
    
    // Eliminar campaña
    $(document).on('click', '.btn-eliminar', function() {
        const idCampana = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`${BASE_URL}/campanas/eliminar/${idCampana}`, function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: 'La campaña ha sido eliminada correctamente',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        // Eliminar fila de la tabla
                        campaignsTable.row(`[data-campaign-id="${idCampana}"]`).remove().draw();
                        
                        // Actualizar contadores
                        const currentCount = parseInt($('#cardTotalCampanas').text()) - 1;
                        $('#cardTotalCampanas').text(currentCount);
                    }
                });
            }
        });
    });
    
    // Exportar datos
    $('#exportBtn').on('click', function() {
        const selectedFormat = 'excel'; // Por defecto exportar a Excel
        
        $.get(`${BASE_URL}/campanas/exportar?formato=${selectedFormat}`, function(response) {
            if (response.success) {
                window.location.href = response.fileUrl;
            }
        });
    });
});