<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home
$routes->get('/', 'Home::index');

// PERSONAS
$routes->get('personas', 'PersonaController::index');               // Listado
$routes->get('personas/crear', 'PersonaController::crear');         // Crear persona
$routes->get('personas/editar/(:num)', 'PersonaController::crear/$1'); // Editar persona
$routes->post('personas/guardar', 'PersonaController::guardar');   // Guardar persona
$routes->get('personas/eliminar/(:num)', 'PersonaController::eliminar/$1'); // Eliminar persona
$routes->get('persona/convertir-a-lead/(:num)', 'PersonaController::convertirALead/$1'); // Convertir persona a lead
$routes->get('leads/registrar/(:num)', 'LeadController::registrar/$1');
$routes->get('personas/editar/(:num)', 'PersonaController::editar/$1');
$routes->post('personas/guardar', 'PersonaController::guardar');
// UBICACION (Departamentos / Provincias / Distritos)
$routes->get('ubicacion/provincias/(:num)', 'UbicacionController::getProvincias/$1');
$routes->get('ubicacion/distritos/(:num)', 'UbicacionController::getDistritos/$1');
// Lead modal
$routes->get('leads/modals/(:num)', 'LeadController::modalCrear/$1');
$routes->get('leads/modals/(:num)', 'LeadController::modalCrear/$1');
$routes->post('leads/guardar', 'LeadController::guardar');
// API para búsqueda DNI
$routes->get('api/personas/buscardni/(:num)', 'PersonaController::BuscadorDni/$1');
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
// LEADS
$routes->get('leads', 'LeadController::index');          // Vista Kanban
$routes->get('leads/crear/(:num)', 'LeadController::crear/$1'); // Crear lead a partir de persona
$routes->post('lead/guardar', 'LeadController::guardar');  // alias en singular
$routes->post('leads/guardar', 'LeadController::guardar'); // oficial en plural
$routes->get('lead/detalle/(:num)', 'LeadController::detalle/$1');

$routes->get('lead/verificar-duplicado/(:num)', 'LeadController::verificarDuplicado/$1');
$routes->post('lead/guardarTarea', 'LeadController::guardarTarea');
$routes->post('lead/eliminar', 'LeadController::eliminar'); 

$routes->get('usuarios','UsuarioController::index');
$routes->get('usuarios/crear','UsuarioController::crear');