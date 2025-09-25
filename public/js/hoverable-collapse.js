/**
 * Hoverable Collapse JavaScript
 * Sistema Delafiber - Funcionalidad de menús desplegables
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initializeHoverableCollapse();
    });

    /**
     * Inicializar collapse con hover
     */
    function initializeHoverableCollapse() {
        // Solo aplicar hover en pantallas grandes
        if ($(window).width() > 991) {
            $('.hoverable-collapse').each(function() {
                const collapseElement = $(this);
                const trigger = $('[data-target="#' + collapseElement.attr('id') + '"]');
                
                // Hover sobre el trigger
                trigger.hover(
                    function() {
                        // Mouse enter
                        if (!collapseElement.hasClass('show')) {
                            collapseElement.collapse('show');
                        }
                    },
                    function() {
                        // Mouse leave con delay
                        setTimeout(function() {
                            if (!collapseElement.is(':hover') && !trigger.is(':hover')) {
                                collapseElement.collapse('hide');
                            }
                        }, 200);
                    }
                );

                // Hover sobre el elemento collapse
                collapseElement.hover(
                    function() {
                        // Mouse enter - mantener abierto
                    },
                    function() {
                        // Mouse leave
                        setTimeout(function() {
                            if (!collapseElement.is(':hover') && !trigger.is(':hover')) {
                                collapseElement.collapse('hide');
                            }
                        }, 200);
                    }
                );
            });
        }
    }

    // Re-inicializar en cambio de tamaño de ventana
    $(window).on('resize', function() {
        if ($(window).width() <= 991) {
            $('.hoverable-collapse').off('mouseenter mouseleave');
            $('[data-target^="#"]').off('mouseenter mouseleave');
        } else {
            initializeHoverableCollapse();
        }
    });

})(jQuery);