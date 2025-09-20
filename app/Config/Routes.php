<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home
$routes->get('/', 'Home::index');

// PERSONAS
$routes->get('personas', 'PersonaController::index');               // Listado
$routes->get('personas/crear', 'PersonaController::crear');  
$routes->post('personas/guardar', 'PersonaController::guardar');    // Crear / Actualizar persona
$routes->get('personas/editar/(:num)', 'PersonaController::editar/$1'); // Editar persona
$routes->post('personas/eliminar/(:num)', 'PersonaController::eliminar/$1');
// API para búsqueda DNI
$routes->get('personas/buscardni/(:num)', 'PersonaController::BuscadorDni/$1');
$routes->get('personas/buscadordni/(:num)', 'PersonaController::BuscadorDni/$1'); // opción adicional si ya la tenías
$routes->get('personas', 'PersonaController::index');
$routes->get('leads/modalCrear/(:any)', 'LeadController::modalCrear/$1');

// --- RUTAS NUEVAS ---
// Agrega esta línea para manejar las peticiones POST a la URL /personas.
// Esto dirigirá cualquier envío de formulario a la función 'store' en tu PersonaController.
$routes->post('personas', 'PersonaController::guardar');
// UBICACION (Departamentos / Provincias / Distritos)
$routes->get('ubicacion/provincias/(:num)', 'UbicacionController::getProvincias/$1');
$routes->get('ubicacion/distritos/(:num)', 'UbicacionController::getDistritos/$1');
// API para búsqueda DNI
$routes->get('api/personas/buscardni/(:num)', 'PersonaController::BuscadorDni/$1');
$routes->get('personas/buscadordni/(:num)', 'PersonaController::BuscadorDni/$1');
// CAMPAÑAS - Rutas corregidas y consistentes
$routes->get('campanas', 'CampanaController::index');               // Listado principal
$routes->get('campanas/crear', 'CampanaController::crear');          // Formulario crear
$routes->get('campanas/crear/(:num)', 'CampanaController::crear/$1'); // Formulario editar
$routes->post('campanas/guardar', 'CampanaController::guardar');     // Crear/actualizar
$routes->get('campanas/detalle/(:num)', 'CampanaController::detalle/$1'); // Detalle para modal
$routes->post('campanas/estado/(:num)', 'CampanaController::estado/$1');   // Cambiar estado
$routes->delete('campanas/eliminar/(:num)', 'CampanaController::eliminar/$1'); // Eliminar
$routes->get('campanas/resumen', 'CampanaController::resumen');      // Métricas dashboard
$routes->get('campanas/exportar', 'CampanaController::exportar');    // Exportar CSV

// MEDIOS - Para gestión de medios de difusión
$routes->post('medios/guardar', 'MedioController::guardar');         // Crear nuevo medio

// USUARIOS
$routes->get('/login', 'LoginController::index');
$routes->post('/login/auth', 'LoginController::auth');
$routes->get('/logout', 'LoginController::logout');

$routes->group('', ['filter' => 'auth'], function($routes){
    $routes->get('campana', 'CampanaController::index');
    $routes->get('persona', 'PersonaController::index');
});
// Lead modal
$routes->get('leads', 'LeadController::index');          // Vista Kanban
$routes->get('lead/detalles/(:num)', 'LeadController::detalle/$1');
$routes->get('leads/modalCrear/(:num)', 'LeadController::modalCrear/$1');
$routes->get('leads/modalCrear/(:num)', 'LeadController::convertirALead/$1');
$routes->post('lead/actualizarEtapa', 'LeadController::actualizarEtapa');
$routes->get('leads/modalCrear/(:num)', 'LeadController::modalCrear/$1'); //Modals cre crear Persona
$routes->get('leads/registrar/(:num)', 'LeadController::registrar/$1');
$routes->post('leads/guardar', 'LeadController::guardar');
$routes->get('leads/index', 'LeadController::index');
$routes->get('lead/detalle/(:num)', 'LeadController::detalle/$1');
$routes->get('lead/verificar-duplicado/(:num)', 'LeadController::verificarDuplicado/$1');
$routes->post('lead/guardarTarea', 'LeadController::guardarTarea');
$routes->post('lead/eliminar', 'LeadController::eliminar'); 
$routes->post('lead/guardarSeguimiento', 'LeadController::guardarSeguimiento');
$routes->post('personas/guardarLead', 'PersonaController::guardarLead');

$routes->get('usuarios','UsuarioController::index');
$routes->get('usuarios/crear','UsuarioController::crear');

// Asegúrate de tener estas rutas
$routes->get('personas', 'PersonaController::index');
$routes->get('personas/crear', 'PersonaController::crear');
$routes->post('personas/guardar', 'PersonaController::guardar');
$routes->get('personas/buscardni/(:num)', 'PersonaController::buscardni/$1');
$routes->get('personas/buscarAjax', 'PersonaController::buscarAjax');
$routes->post('personas/eliminar/(:num)', 'PersonaController::eliminar/$1');
$routes->get('personas/modalCrear/(:num)', 'PersonaController::modalCrear/$1');
$routes->post('persona/guardarLead', 'PersonaController::guardarLead'); 
$routes->get('personas/buscadorDni', 'PersonaController::buscadorDni');
$routes->get('personas/buscardni', 'PersonaController::buscardni');
$routes->get('personas/eliminar/(:num)', 'PersonaController::eliminar/$1');
$routes->get('personas/buscarAjax', 'PersonaController::buscarAjax');



// === NUEVA RUTA PARA EL MODAL CREAR LEAD ===
$routes->get('personas/modalCrear/(:num)', 'PersonaController::modalCrear/$1');

// === NUEVA RUTA PARA GUARDAR EL LEAD ===
$routes->post('persona/guardarLead', 'PersonaController::guardarLead');

// Rutas para Leads
$routes->get('leads', 'LeadController::index');
$routes->get('leads/modal-crear', 'LeadController::modalCrear');
$routes->post('leads/guardar', 'LeadController::guardar');
$routes->post('leads/moverEtapa', 'LeadController::moverEtapa'); // Nueva ruta para drag & drop
// app/Config/Routes.php
$routes->group('campanas', function($routes) {
$routes->get('/', 'CampanaController::index');
$routes->get('crear', 'CampanaController::crear');
$routes->post('guardar', 'CampanaController::guardar');
$routes->get('detalle/(:num)', 'CampanaController::detalle/$1');
$routes->post('estado/(:num)', 'CampanaController::cambiarEstado/$1');
$routes->get('resumen', 'CampanaController::resumen');
$routes->delete('eliminar/(:num)', 'CampanaController::eliminar/$1');
});

//Dashboard
$routes->get('dashboard/index', 'DashboardController::index');

$routes->get('tarea/tarea', 'TareaController::index');
$routes->post('tareas/crear', 'TareaController::crear');
$routes->post('tareas/editar/(:num)', 'TareaController::editar/$1');
$routes->delete('tareas/eliminar/(:num)', 'TareaController::eliminar/$1');
$routes->post('tareas/cambiarEstado/(:num)', 'TareaController::cambiarEstado/$1');
$routes->get('tareas/calendario', 'TareaController::calendario');


// Rutas para tareas
$routes->get('tareas', 'TareaController::index');
$routes->post('tareas/crear', 'TareaController::crear');
$routes->post('tareas/editar/(:num)', 'TareaController::editar/$1');  
$routes->delete('tareas/eliminar/(:num)', 'TareaController::eliminar/$1');
$routes->post('tareas/cambiarEstado/(:num)', 'TareaController::cambiarEstado/$1');
$routes->get('tareas/calendario', 'TareaController::calendario');

$routes->get('usuarios', 'UsuarioController::index');
$routes->post('usuarios/crear', 'UsuarioController::crear');
$routes->post('usuarios/editar/(:num)', 'UsuarioController::editar/$1');
$routes->delete('usuarios/eliminar/(:num)', 'UsuarioController::eliminar/$1');
$routes->post('usuarios/cambiarEstado/(:num)', 'UsuarioController::cambiarEstado/$1');

$routes->group('campanas', function($routes) {
    // Vista principal
    $routes->get('/', 'CampanaController::index');
    
    // CRUD básico
    $routes->get('crear', 'CampanaController::crear');
    $routes->get('crear/(:num)', 'CampanaController::crear/$1'); // Editar
    $routes->post('guardar', 'CampanaController::guardar');
    
    // API endpoints
    $routes->get('detalle/(:num)', 'CampanaController::detalle/$1');
    $routes->post('estado/(:num)', 'CampanaController::cambiarEstado/$1');
    $routes->delete('eliminar/(:num)', 'CampanaController::eliminar/$1');
    
    // Datos y reportes
    $routes->get('resumen', 'CampanaController::resumen');
    $routes->get('datos', 'CampanaController::getCampanas');
    $routes->get('exportar', 'CampanaController::exportar');
    
    // Funciones adicionales
    $routes->post('duplicar/(:num)', 'CampanaController::duplicar/$1');
    $routes->get('analytics', 'CampanaController::analytics');
});
$routes->group('campanas', function($routes) {
    // Vista principal
    $routes->get('/', 'CampanaController::index');
    
    // CRUD básico
    $routes->get('crear', 'CampanaController::crear');
    $routes->get('crear/(:num)', 'CampanaController::crear/$1'); // Editar
    $routes->post('guardar', 'CampanaController::guardar');
    
    // API endpoints
    $routes->get('detalle/(:num)', 'CampanaController::detalle/$1');
    $routes->post('estado/(:num)', 'CampanaController::estado/$1');
    $routes->delete('eliminar/(:num)', 'CampanaController::eliminar/$1');
    
    // Datos y reportes
    $routes->get('resumen', 'CampanaController::resumen');
    $routes->get('datos', 'CampanaController::getCampanas');
    $routes->get('exportar', 'CampanaController::exportar');
    
    // Funciones adicionales
    $routes->post('duplicar/(:num)', 'CampanaController::duplicar/$1');
    $routes->get('analytics', 'CampanaController::analytics');
});

// ================================
// RUTAS ALTERNATIVAS (para compatibilidad)
// ================================
// Si en algún lugar usas 'campana' en lugar de 'campanas'
$routes->group('campana', function($routes) {
    $routes->get('detalle/(:num)', 'CampanaController::detalle/$1');
    $routes->post('estado/(:num)', 'CampanaController::estado/$1');
    $routes->post('guardar', 'CampanaController::guardar');
    $routes->get('resumen', 'CampanaController::resumen');
});

// ================================
// OTRAS RUTAS DEL SISTEMA
// ================================
$routes->group('usuarios', function($routes) {
    $routes->get('/', 'UsuarioController::index');
    $routes->get('crear', 'UsuarioController::crear');
    $routes->post('guardar', 'UsuarioController::guardar');
});

$routes->group('leads', function($routes) {
    $routes->get('/', 'LeadController::index');
    $routes->get('crear', 'LeadController::crear');
    $routes->post('guardar', 'LeadController::guardar');
});

$routes->group('medios', function($routes) {
    $routes->get('/', 'MedioController::index');
    $routes->post('crear', 'MedioController::crear');
    $routes->get('activos', 'MedioController::getActivos');
});

// ================================
// RUTAS DE AUTENTICACIÓN
// ================================
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

// ================================
// RUTAS DE API (opcional para AJAX)
// ================================
$routes->group('api', function($routes) {
    $routes->group('campanas', function($routes) {
        $routes->get('/', 'CampanaController::getCampanas');
        $routes->post('/', 'CampanaController::guardar');
        $routes->get('(:num)', 'CampanaController::detalle/$1');
        $routes->put('(:num)', 'CampanaController::guardar');
        $routes->delete('(:num)', 'CampanaController::eliminar/$1');
        
        $routes->get('metricas/resumen', 'CampanaController::resumen');
        $routes->get('exportar/(:alpha)', 'CampanaController::exportar/$1');
    });
});

// ================================
// FILTROS GLOBALES
// ================================
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true); // Solo en desarrollo

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 * Rutas adicionales específicas del proyecto
 */

// Ruta de dashboard principal
$routes->get('dashboard', 'DashboardController::index');

// Rutas de reportes
$routes->group('reportes', function($routes) {
    $routes->get('campanas', 'ReporteController::campanas');
    $routes->get('leads', 'ReporteController::leads');
    $routes->get('ventas', 'ReporteController::ventas');
});

// Rutas de configuración
$routes->group('config', function($routes) {
    $routes->get('/', 'ConfigController::index');
    $routes->post('actualizar', 'ConfigController::actualizar');
});

// ================================
// MIDDLEWARE/FILTROS
// ================================
// Aplicar filtros de autenticación a rutas protegidas
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Todas las rutas de campañas requieren autenticación
    $routes->group('campanas', function($routes) {
        // Las rutas ya están definidas arriba
    });
    
    $routes->get('dashboard', 'DashboardController::index');
    $routes->group('reportes', function($routes) {
        // Las rutas ya están definidas arriba
    });
});

// ================================
// RUTAS DE DESARROLLO/DEBUG
// ================================
if (ENVIRONMENT === 'development') {
    $routes->get('debug/campanas', function() {
        $controller = new \App\Controllers\CampanaController();
        return $controller->resumen();
    });
    
    $routes->get('test/db', function() {
        $db = \Config\Database::connect();
        try {
            $query = $db->query("SELECT COUNT(*) as total FROM campanias");
            $result = $query->getRow();
            return "Conexión exitosa. Total campañas: " . $result->total;
        } catch (\Exception $e) {
            return "Error de conexión: " . $e->getMessage();
        }
    });
}