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
// CAMPANAS
$routes->get('campanas', 'CampanaController::index');               // Listado
$routes->get('campana/crear', 'CampanaController::crear');
$routes->get('campana/form/(:num)', 'CampanaController::form/$1'); // para editar
$routes->post('campana/guardar', 'CampanaController::guardar');
$routes->get('campana/detalleMedios/(:num)', 'CampanaController::detalleMedios/$1');
$routes->get('campana/medios/(:num)', 'CampanaController::getMediosCampana/$1');
$routes->get('campana/eliminar/(:num)', 'CampanaController::eliminar/$1'); // Eliminar campaña
$routes->post('campanas/cambiar-estado', 'CampanaController::cambiarEstado');
$routes->post('medio/guardarMedio', 'MedioController::guardarMedio');
$routes->post('campana/estado/(:num)', 'CampanaController::cambiarEstado/$1');
$routes->get('campana/detalle/(:num)', 'CampanaController::detalle/$1');
$routes->get('campana/resumen', 'Campana::resumen');

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

$routes->get('personas', 'PersonaController::index');
$routes->get('personas/crear', 'PersonaController::crear');
$routes->post('personas/guardar', 'PersonaController::guardar');
$routes->get('personas/buscardni/(:num)', 'PersonaController::buscarDni/$1');
$routes->post('personas/eliminar/(:num)', 'PersonaController::eliminar/$1');
$routes->get('personas/editar/(:num)', 'PersonaController::editar/$1');
$routes->post('personas/actualizar', 'PersonaController::actualizar');
$routes->get('personas/buscarAjax', 'PersonaController::buscarAjax');

// === NUEVA RUTA PARA EL MODAL CREAR LEAD ===
$routes->get('personas/modalCrear/(:num)', 'PersonaController::modalCrear/$1');

// === NUEVA RUTA PARA GUARDAR EL LEAD ===
$routes->post('persona/guardarLead', 'PersonaController::guardarLead');

// Rutas para Leads
$routes->get('leads', 'LeadController::index');
$routes->get('leads/modal-crear', 'LeadController::modalCrear');
$routes->post('leads/guardar', 'LeadController::guardar');

//Dashboard
$routes->get('dashboard/index', 'DashboardController::index');