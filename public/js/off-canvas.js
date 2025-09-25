/**
 * Off Canvas JavaScript - Funcionalidad del Sidebar
 * Sistema Delafiber - Gestión de Fibra Óptica
 */

(function($) {
    'use strict';

    // Inicialización del sidebar
    $(document).ready(function() {
        initializeSidebar();
        initializeOffCanvas();
        setActiveMenuItem();
    });

    /**
     * Inicializar funcionalidades del sidebar
     */
    function initializeSidebar() {
        // Toggle sidebar en desktop
        $('[data-toggle="minimize"]').on('click', function() {
            $('body').toggleClass('sidebar-collapse');
            
            // Guardar estado en localStorage
            const isCollapsed = $('body').hasClass('sidebar-collapse');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
        });

        // Restaurar estado del sidebar
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
            $('body').addClass('sidebar-collapse');
        }

        // Hover en sidebar colapsado
        if ($('body').hasClass('sidebar-collapse')) {
            $('.sidebar').on('mouseenter', function() {
                $('body').addClass('sidebar-hover');
            }).on('mouseleave', function() {
                $('body').removeClass('sidebar-hover');
            });
        }
    }

    /**
     * Inicializar off-canvas para móviles
     */
    function initializeOffCanvas() {
        // Toggle sidebar en móvil
        $('[data-toggle="offcanvas"]').on('click', function() {
            $('body').toggleClass('sidebar-open');
        });

        // Cerrar sidebar al hacer clic en overlay
        $('.main-panel').on('click', function() {
            if ($('body').hasClass('sidebar-open')) {
                $('body').removeClass('sidebar-open');
            }
        });

        // Cerrar sidebar al hacer clic en un enlace del menú en móvil
        $('.sidebar .nav-link').on('click', function() {
            if ($(window).width() <= 991) {
                $('body').removeClass('sidebar-open');
            }
        });
    }

    /**
     * Establecer elemento activo del menú
     */
    function setActiveMenuItem() {
        const currentPath = window.location.pathname;
        const currentUrl = window.location.href;
        
        $('.sidebar .nav-link').each(function() {
            const linkHref = $(this).attr('href');
            
            // Remover clase active de todos los elementos
            $(this).removeClass('active');
            $(this).parent().removeClass('active');
            
            // Verificar si el enlace coincide con la URL actual
            if (linkHref && (currentUrl.includes(linkHref) || currentPath.includes(linkHref))) {
                $(this).addClass('active');
                $(this).parent().addClass('active');
                
                // Si es un submenú, activar también el padre
                const parentCollapse = $(this).closest('.collapse');
                if (parentCollapse.length) {
                    parentCollapse.addClass('show');
                    parentCollapse.prev('.nav-link').addClass('active');
                    parentCollapse.prev('.nav-link').attr('aria-expanded', 'true');
                }
            }
        });
    }

    /**
     * Funcionalidad de submenús colapsables
     */
    function initializeSubmenus() {
        $('.nav-link[data-toggle="collapse"]').on('click', function(e) {
            e.preventDefault();
            
            const target = $(this).attr('href');
            const isExpanded = $(this).attr('aria-expanded') === 'true';
            
            // Cerrar otros submenús
            $('.nav-link[data-toggle="collapse"]').not(this).attr('aria-expanded', 'false');
            $('.collapse.show').not(target).removeClass('show');
            
            // Toggle este submenú
            $(this).attr('aria-expanded', !isExpanded);
            $(target).toggleClass('show');
        });
    }

    /**
     * Funcionalidad de tooltips para sidebar colapsado
     */
    function initializeTooltips() {
        if ($('body').hasClass('sidebar-collapse')) {
            $('.sidebar .nav-link').tooltip({
                placement: 'right',
                title: function() {
                    return $(this).find('.menu-title').text();
                }
            });
        }
    }

    /**
     * Responsive behavior
     */
    $(window).on('resize', function() {
        if ($(window).width() > 991) {
            $('body').removeClass('sidebar-open');
        }
    });

    // Re-inicializar tooltips cuando cambie el estado del sidebar
    $('[data-toggle="minimize"]').on('click', function() {
        setTimeout(function() {
            $('.sidebar .nav-link').tooltip('dispose');
            initializeTooltips();
        }, 300);
    });

})(jQuery);