<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Home
$routes->get('/', 'Home::index');
$routes->get('dashboard', 'DashboardController::index');
$routes->get('dashboard/index', 'DashboardController::index');

// Login
$routes->get('login', 'LoginController::index');
$routes->post('login/auth', 'LoginController::auth');
$routes->get('logout', 'LoginController::logout');

// Personas
$routes->group('personas', function($routes) {
    $routes->get('/', 'PersonaController::index');
    $routes->get('crear', 'PersonaController::crear');
    $routes->get('editar/(:num)', 'PersonaController::editar/$1');
    $routes->post('guardar', 'PersonaController::guardar');
    $routes->post('eliminar/(:num)', 'PersonaController::eliminar/$1');
    $routes->get('buscardni/(:num)', 'PersonaController::BuscadorDni/$1');
    $routes->get('buscarAjax', 'PersonaController::buscarAjax');
    $routes->get('modalCrear/(:num)', 'PersonaController::modalCrear/$1');
    $routes->post('guardarLead', 'PersonaController::guardarLead');
});

$routes->post('persona/guardarLead', 'PersonaController::guardarLead');

// Campañas
$routes->group('campanas', function($routes) {
    $routes->get('/', 'CampanaController::index');
    $routes->get('crear', 'CampanaController::crear');
    $routes->get('crear/(:num)', 'CampanaController::crear/$1');
    $routes->post('guardar', 'CampanaController::guardar');
    $routes->delete('eliminar/(:num)', 'CampanaController::eliminar/$1');
    $routes->get('detalle/(:num)', 'CampanaController::detalle/$1');
    $routes->post('estado/(:num)', 'CampanaController::estado/$1');
    $routes->get('resumen', 'CampanaController::resumen');
    $routes->get('datos', 'CampanaController::getCampanas');
    $routes->get('exportar', 'CampanaController::exportar');
    $routes->post('duplicar/(:num)', 'CampanaController::duplicar/$1');
    $routes->get('analytics', 'CampanaController::analytics');
});

// Leads
$routes->group('leads', function($routes) {
    $routes->get('/', 'LeadController::index');
    $routes->get('crear', 'LeadController::crear');
    $routes->get('registrar/(:num)', 'LeadController::registrar/$1');
    $routes->post('guardar', 'LeadController::guardar');
    $routes->post('eliminar', 'LeadController::eliminar');
    $routes->get('detalle/(:num)', 'LeadController::detalle/$1');
    $routes->get('modalCrear/(:num)', 'LeadController::modalCrear/$1');
    $routes->get('verificar-duplicado/(:num)', 'LeadController::verificarDuplicado/$1');
    $routes->post('actualizarEtapa', 'LeadController::actualizarEtapa');
    $routes->post('moverEtapa', 'LeadController::moverEtapa');
    $routes->post('guardarTarea', 'LeadController::guardarTarea');
    $routes->post('guardarSeguimiento', 'LeadController::guardarSeguimiento');
});

// Tareas
$routes->get('tarea/tarea', 'TareaController::index');
$routes->group('tareas', function($routes) {
    $routes->get('/', 'TareaController::index');
    $routes->post('crear', 'TareaController::crear');
    $routes->post('editar/(:num)', 'TareaController::editar/$1');
    $routes->delete('eliminar/(:num)', 'TareaController::eliminar/$1');
    $routes->post('cambiarEstado/(:num)', 'TareaController::cambiarEstado/$1');
    $routes->get('calendario', 'TareaController::calendario');
});

// Usuarios
$routes->group('usuarios', function($routes) {
    $routes->get('/', 'UsuarioController::index');
    $routes->get('crear', 'UsuarioController::crear');
    $routes->post('crear', 'UsuarioController::crear');
    $routes->post('editar/(:num)', 'UsuarioController::editar/$1');
    $routes->delete('eliminar/(:num)', 'UsuarioController::eliminar/$1');
    $routes->post('cambiarEstado/(:num)', 'UsuarioController::cambiarEstado/$1');
});

// Ubicación
$routes->group('ubicacion', function($routes) {
    $routes->get('provincias/(:num)', 'UbicacionController::getProvincias/$1');
    $routes->get('distritos/(:num)', 'UbicacionController::getDistritos/$1');
});

// Medios
$routes->group('medios', function($routes) {
    $routes->get('/', 'MedioController::index');
    $routes->post('guardar', 'MedioController::guardar');
    $routes->get('activos', 'MedioController::getActivos');
});

// API
$routes->group('api', function($routes) {
    $routes->get('personas/buscardni/(:num)', 'PersonaController::BuscadorDni/$1');
    $routes->group('campanas', function($routes) {
        $routes->get('/', 'CampanaController::getCampanas');
        $routes->get('(:num)', 'CampanaController::detalle/$1');
        $routes->post('/', 'CampanaController::guardar');
        $routes->put('(:num)', 'CampanaController::guardar');
        $routes->delete('(:num)', 'CampanaController::eliminar/$1');
        $routes->get('metricas/resumen', 'CampanaController::resumen');
    });
});

// Compatibilidad
$routes->get('campana/(:any)', 'CampanaController::$1');
$routes->get('persona/(:any)', 'PersonaController::$1');

// Rutas adicionales del menú
$routes->get('oportunidades', 'OportunidadesController::index');
$routes->get('clientes', 'ClientesController::index');
$routes->get('mensajes', 'MensajesController::index');
$routes->get('reportes', 'ReportesController::index');
$routes->get('configuracion', 'ConfiguracionController::index');

// Desarrollo
if (ENVIRONMENT === 'development') {
    $routes->get('debug/routes', function() {
        $routes = service('routes');
        $collection = $routes->getRoutes();
        echo "<pre>";
        print_r($collection);
        echo "</pre>";
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