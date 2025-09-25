/**
 * Template JavaScript - Funcionalidades del Header y Layout
 * Sistema Delafiber - Gestión de Fibra Óptica
 */

(function($) {
    'use strict';

    // Inicialización cuando el documento esté listo
    $(document).ready(function() {
        initializeSearch();
        initializeNotifications();
        initializeProfileDropdown();
        initializeSidebarToggle();
        initializeResponsiveMenu();
    });

    /**
     * Funcionalidad de búsqueda en el header
     */
    function initializeSearch() {
        const searchInput = $('#navbar-search-input');
        const searchIcon = $('#navbar-search-icon');
        
        // Búsqueda en tiempo real
        searchInput.on('input', function() {
            const query = $(this).val().trim();
            
            if (query.length >= 2) {
                performSearch(query);
            } else {
                clearSearchResults();
            }
        });

        // Activar búsqueda con Enter
        searchInput.on('keypress', function(e) {
            if (e.which === 13) {
                const query = $(this).val().trim();
                if (query.length >= 2) {
                    performSearch(query);
                }
            }
        });

        // Búsqueda con clic en icono
        searchIcon.on('click', function() {
            const query = searchInput.val().trim();
            if (query.length >= 2) {
                performSearch(query);
            }
        });
    }

    /**
     * Realizar búsqueda AJAX
     */
    function performSearch(query) {
        $.ajax({
            url: baseUrl + 'dashboard/buscar',
            method: 'POST',
            data: {
                query: query,
                csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            beforeSend: function() {
                $('#navbar-search-icon .icon-search').removeClass('icon-search').addClass('spinner-border spinner-border-sm');
            },
            success: function(response) {
                if (response.success) {
                    showSearchResults(response.data);
                } else {
                    showSearchError('No se encontraron resultados');
                }
            },
            error: function() {
                showSearchError('Error en la búsqueda');
            },
            complete: function() {
                $('#navbar-search-icon .spinner-border').removeClass('spinner-border spinner-border-sm').addClass('icon-search');
            }
        });
    }

    /**
     * Mostrar resultados de búsqueda
     */
    function showSearchResults(results) {
        let html = '<div class="search-results dropdown-menu show" style="position: absolute; top: 100%; left: 0; right: 0; z-index: 1000;">';
        
        if (results.length > 0) {
            html += '<h6 class="dropdown-header">Resultados encontrados</h6>';
            
            results.forEach(function(item) {
                html += `
                    <a class="dropdown-item search-result-item" href="${item.url}">
                        <div class="d-flex align-items-center">
                            <i class="${item.icon} mr-2"></i>
                            <div>
                                <div class="font-weight-bold">${item.title}</div>
                                <small class="text-muted">${item.subtitle}</small>
                            </div>
                        </div>
                    </a>
                `;
            });
        } else {
            html += '<div class="dropdown-item-text">No se encontraron resultados</div>';
        }
        
        html += '</div>';
        
        // Remover resultados anteriores y agregar nuevos
        $('.search-results').remove();
        $('#navbar-search-input').parent().append(html);
        
        // Auto-ocultar después de 10 segundos
        setTimeout(clearSearchResults, 10000);
    }

    /**
     * Mostrar error de búsqueda
     */
    function showSearchError(message) {
        const html = `
            <div class="search-results dropdown-menu show" style="position: absolute; top: 100%; left: 0; right: 0; z-index: 1000;">
                <div class="dropdown-item-text text-danger">
                    <i class="ti-alert mr-2"></i>${message}
                </div>
            </div>
        `;
        
        $('.search-results').remove();
        $('#navbar-search-input').parent().append(html);
        
        setTimeout(clearSearchResults, 5000);
    }

    /**
     * Limpiar resultados de búsqueda
     */
    function clearSearchResults() {
        $('.search-results').fadeOut(300, function() {
            $(this).remove();
        });
    }

    /**
     * Funcionalidad de notificaciones
     */
    function initializeNotifications() {
        loadNotifications();
        
        // Actualizar notificaciones cada 30 segundos
        setInterval(loadNotifications, 30000);
        
        // Marcar notificación como leída al hacer clic
        $(document).on('click', '.notification-item', function() {
            const notificationId = $(this).data('id');
            markAsRead(notificationId);
        });
    }

    /**
     * Cargar notificaciones
     */
    function loadNotifications() {
        $.ajax({
            url: baseUrl + 'dashboard/notificaciones',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateNotificationBadge(response.unread_count);
                    updateNotificationList(response.notifications);
                }
            },
            error: function() {
                console.log('Error al cargar notificaciones');
            }
        });
    }

    /**
     * Actualizar badge de notificaciones
     */
    function updateNotificationBadge(count) {
        const badge = $('#notificationDropdown .count');
        
        if (count > 0) {
            badge.text(count).show();
        } else {
            badge.hide();
        }
    }

    /**
     * Actualizar lista de notificaciones
     */
    function updateNotificationList(notifications) {
        const container = $('#notificationDropdown').next('.dropdown-menu');
        let html = '<p class="mb-0 font-weight-normal float-left dropdown-header">Notificaciones</p>';
        
        if (notifications.length > 0) {
            notifications.forEach(function(notification) {
                html += `
                    <a class="dropdown-item preview-item notification-item" data-id="${notification.id}">
                        <div class="preview-thumbnail">
                            <div class="preview-icon ${notification.color}">
                                <i class="${notification.icon} mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">${notification.title}</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">${notification.time}</p>
                        </div>
                    </a>
                `;
            });
        } else {
            html += '<div class="dropdown-item-text">No hay notificaciones</div>';
        }
        
        container.html(html);
    }

    /**
     * Marcar notificación como leída
     */
    function markAsRead(notificationId) {
        $.ajax({
            url: baseUrl + 'dashboard/marcar-leida',
            method: 'POST',
            data: {
                notification_id: notificationId,
                csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    loadNotifications(); // Recargar notificaciones
                }
            }
        });
    }

    /**
     * Funcionalidad del dropdown de perfil
     */
    function initializeProfileDropdown() {
        // Cargar información del usuario
        loadUserProfile();
    }

    /**
     * Cargar perfil del usuario
     */
    function loadUserProfile() {
        $.ajax({
            url: baseUrl + 'dashboard/perfil',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateUserProfile(response.user);
                }
            }
        });
    }

    /**
     * Actualizar información del perfil
     */
    function updateUserProfile(user) {
        // Actualizar imagen de perfil si existe
        if (user.foto) {
            $('#profileDropdown img').attr('src', baseUrl + 'uploads/profiles/' + user.foto);
        }
        
        // Actualizar nombre en el dropdown si existe
        const profileName = $('.navbar-dropdown .user-name');
        if (profileName.length && user.nombre) {
            profileName.text(user.nombre);
        }
    }

    /**
     * Toggle del sidebar - DESHABILITADO 
     */
    function initializeSidebarToggle() {
        // Funcionalidad movida a off-canvas.js para evitar conflictos
        console.log('Sidebar toggle manejado por off-canvas.js');
    }

    /**
     * Menú responsivo
     */
    function initializeResponsiveMenu() {
        // Cerrar dropdown al hacer clic fuera
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });
        
        // Toggle manual de dropdowns
        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            const dropdown = $(this).next('.dropdown-menu');
            
            // Cerrar otros dropdowns
            $('.dropdown-menu').not(dropdown).removeClass('show');
            
            // Toggle este dropdown
            dropdown.toggleClass('show');
        });
    }

    // Variable global para baseUrl (debe definirse en el layout principal)
    if (typeof baseUrl === 'undefined') {
        window.baseUrl = window.location.origin + '/';
    }

})(jQuery);