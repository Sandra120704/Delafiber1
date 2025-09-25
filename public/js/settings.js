/**
 * Settings JavaScript - Configuración del sistema
 * Sistema Delafiber - Gestión de Fibra Óptica
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initializeSettings();
        loadUserPreferences();
    });

    /**
     * Inicializar configuraciones
     */
    function initializeSettings() {
        // Toggle de configuraciones rápidas
        initializeQuickSettings();
        
        // Configuración de tema
        initializeThemeSettings();
        
        // Configuración de notificaciones
        initializeNotificationSettings();
        
        // Configuración de idioma
        initializeLanguageSettings();
    }

    /**
     * Configuraciones rápidas
     */
    function initializeQuickSettings() {
        // Toggle sidebar fijo
        $('#fixed-sidebar').on('change', function() {
            const isFixed = $(this).is(':checked');
            $('body').toggleClass('fixed-sidebar', isFixed);
            saveUserPreference('fixed_sidebar', isFixed);
        });

        // Toggle header fijo
        $('#fixed-header').on('change', function() {
            const isFixed = $(this).is(':checked');
            $('body').toggleClass('fixed-header', isFixed);
            saveUserPreference('fixed_header', isFixed);
        });

        // Modo compacto
        $('#compact-mode').on('change', function() {
            const isCompact = $(this).is(':checked');
            $('body').toggleClass('compact-mode', isCompact);
            saveUserPreference('compact_mode', isCompact);
        });
    }

    /**
     * Configuración de tema
     */
    function initializeThemeSettings() {
        // Cambio de tema
        $('.theme-option').on('click', function() {
            const theme = $(this).data('theme');
            changeTheme(theme);
        });

        // Toggle modo oscuro
        $('#dark-mode').on('change', function() {
            const isDark = $(this).is(':checked');
            toggleDarkMode(isDark);
        });
    }

    /**
     * Cambiar tema
     */
    function changeTheme(theme) {
        // Remover temas anteriores
        $('body').removeClass('theme-light theme-dark theme-blue theme-green');
        
        // Aplicar nuevo tema
        $('body').addClass('theme-' + theme);
        
        // Actualizar selección visual
        $('.theme-option').removeClass('active');
        $('.theme-option[data-theme="' + theme + '"]').addClass('active');
        
        // Guardar preferencia
        saveUserPreference('theme', theme);
    }

    /**
     * Toggle modo oscuro
     */
    function toggleDarkMode(isDark) {
        if (isDark) {
            $('body').addClass('dark-mode');
            $('link[href*="style.css"]').attr('href', $('link[href*="style.css"]').attr('href').replace('style.css', 'style-dark.css'));
        } else {
            $('body').removeClass('dark-mode');
            $('link[href*="style-dark.css"]').attr('href', $('link[href*="style-dark.css"]').attr('href').replace('style-dark.css', 'style.css'));
        }
        
        saveUserPreference('dark_mode', isDark);
    }

    /**
     * Configuración de notificaciones
     */
    function initializeNotificationSettings() {
        // Toggle notificaciones del sistema
        $('#system-notifications').on('change', function() {
            const enabled = $(this).is(':checked');
            saveUserPreference('system_notifications', enabled);
            
            if (enabled) {
                requestNotificationPermission();
            }
        });

        // Toggle notificaciones de email
        $('#email-notifications').on('change', function() {
            const enabled = $(this).is(':checked');
            saveUserPreference('email_notifications', enabled);
        });

        // Toggle sonidos
        $('#notification-sounds').on('change', function() {
            const enabled = $(this).is(':checked');
            saveUserPreference('notification_sounds', enabled);
        });
    }

    /**
     * Solicitar permiso para notificaciones
     */
    function requestNotificationPermission() {
        if ('Notification' in window && Notification.permission !== 'granted') {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    showNotification('Notificaciones activadas', 'Ahora recibirás notificaciones del sistema');
                }
            });
        }
    }

    /**
     * Mostrar notificación del sistema
     */
    function showNotification(title, message) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: baseUrl + 'images/logo-delafiber.png',
                badge: baseUrl + 'images/favicon.png'
            });
        }
    }

    /**
     * Configuración de idioma
     */
    function initializeLanguageSettings() {
        $('.language-option').on('click', function(e) {
            e.preventDefault();
            const language = $(this).data('lang');
            changeLanguage(language);
        });
    }

    /**
     * Cambiar idioma
     */
    function changeLanguage(language) {
        $.ajax({
            url: baseUrl + 'configuracion/cambiar-idioma',
            method: 'POST',
            data: {
                language: language,
                csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function() {
                alert('Error al cambiar idioma');
            }
        });
    }

    /**
     * Guardar preferencia del usuario
     */
    function saveUserPreference(key, value) {
        $.ajax({
            url: baseUrl + 'configuracion/guardar-preferencia',
            method: 'POST',
            data: {
                key: key,
                value: value,
                csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (!response.success) {
                    console.log('Error al guardar preferencia: ' + key);
                }
            }
        });

        // También guardar en localStorage como respaldo
        localStorage.setItem('pref_' + key, value);
    }

    /**
     * Cargar preferencias del usuario
     */
    function loadUserPreferences() {
        $.ajax({
            url: baseUrl + 'configuracion/obtener-preferencias',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    applyUserPreferences(response.preferences);
                }
            },
            error: function() {
                // Cargar desde localStorage como respaldo
                loadPreferencesFromLocalStorage();
            }
        });
    }

    /**
     * Aplicar preferencias del usuario
     */
    function applyUserPreferences(preferences) {
        // Aplicar cada preferencia
        Object.keys(preferences).forEach(function(key) {
            const value = preferences[key];
            
            switch(key) {
                case 'fixed_sidebar':
                    $('body').toggleClass('fixed-sidebar', value);
                    $('#fixed-sidebar').prop('checked', value);
                    break;
                case 'fixed_header':
                    $('body').toggleClass('fixed-header', value);
                    $('#fixed-header').prop('checked', value);
                    break;
                case 'compact_mode':
                    $('body').toggleClass('compact-mode', value);
                    $('#compact-mode').prop('checked', value);
                    break;
                case 'theme':
                    changeTheme(value);
                    break;
                case 'dark_mode':
                    toggleDarkMode(value);
                    $('#dark-mode').prop('checked', value);
                    break;
                case 'system_notifications':
                    $('#system-notifications').prop('checked', value);
                    break;
                case 'email_notifications':
                    $('#email-notifications').prop('checked', value);
                    break;
                case 'notification_sounds':
                    $('#notification-sounds').prop('checked', value);
                    break;
            }
        });
    }

    /**
     * Cargar preferencias desde localStorage
     */
    function loadPreferencesFromLocalStorage() {
        const keys = ['fixed_sidebar', 'fixed_header', 'compact_mode', 'theme', 'dark_mode'];
        
        keys.forEach(function(key) {
            const value = localStorage.getItem('pref_' + key);
            if (value !== null) {
                const boolValue = value === 'true';
                
                switch(key) {
                    case 'fixed_sidebar':
                        $('body').toggleClass('fixed-sidebar', boolValue);
                        $('#fixed-sidebar').prop('checked', boolValue);
                        break;
                    case 'fixed_header':
                        $('body').toggleClass('fixed-header', boolValue);
                        $('#fixed-header').prop('checked', boolValue);
                        break;
                    case 'compact_mode':
                        $('body').toggleClass('compact-mode', boolValue);
                        $('#compact-mode').prop('checked', boolValue);
                        break;
                    case 'theme':
                        if (value !== 'true' && value !== 'false') {
                            changeTheme(value);
                        }
                        break;
                    case 'dark_mode':
                        toggleDarkMode(boolValue);
                        $('#dark-mode').prop('checked', boolValue);
                        break;
                }
            }
        });
    }

    // Variable global para baseUrl
    if (typeof baseUrl === 'undefined') {
        window.baseUrl = window.location.origin + '/';
    }

})(jQuery);