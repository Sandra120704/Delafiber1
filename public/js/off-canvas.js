/**
 * Off Canvas JavaScript - Funcionalidad del Sidebar
 * Sistema Delafiber - Gestión de Fibra Óptica
 */

(function($) {
    'use strict';

    // Verificar que jQuery esté disponible
    if (typeof $ === 'undefined') {
        console.error('jQuery no está cargado - off-canvas.js');
        return;
    }

    // Inicialización del sidebar
    $(document).ready(function() {
        console.log('off-canvas.js iniciando...');
        initializeSidebar();
        initializeOffCanvas();
        setActiveMenuItem();
    });

    /**
     * Inicializar funcionalidades del sidebar
     */
    function initializeSidebar() {
        // Toggle sidebar en desktop - CORREGIDO CON CLASE CORRECTA
        $('[data-toggle="minimize"], .navbar-toggler').off('click.sidebar').on('click.sidebar', function(e) {
            e.preventDefault();
            console.log('Hamburger button clicked'); // Debug
            
            $('body').toggleClass('sidebar-icon-only');
            
            // Guardar estado en localStorage
            const isCollapsed = $('body').hasClass('sidebar-icon-only');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
            
            // Debug
            console.log('Sidebar collapsed:', isCollapsed);
        });

        // Restaurar estado del sidebar
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
            $('body').addClass('sidebar-icon-only');
        }

        // Hover en sidebar colapsado - mejorado
        setupSidebarHover();
    }

    /**
     * Configurar hover del sidebar
     */
    function setupSidebarHover() {
        $('.sidebar').off('mouseenter.hover mouseleave.hover').on('mouseenter.hover', function() {
            if ($('body').hasClass('sidebar-icon-only')) {
                $('body').addClass('sidebar-hover');
            }
        }).on('mouseleave.hover', function() {
            $('body').removeClass('sidebar-hover');
        });
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