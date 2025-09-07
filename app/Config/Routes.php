<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('personas', 'PersonaController::index');

$routes->get('campanas', 'CampanaController::index');
$routes->get('campana/form', 'CampanaController::form');
$routes->get('campana/form/(:num)', 'CampanaController::form/$1');
$routes->post('campana/guardar', 'CampanaController::guardar');
$routes->post('campana/eliminar', 'CampanaController::eliminar');
$routes->post('campana/cambiarEstado', 'CampanaController::cambiarEstado');



/* Personas */
$routes->get('personas', 'PersonaController::index');  // Listado
$routes->get('personas/crear', 'PersonaController::crear'); // Crear nueva persona
$routes->get('persona/form/(:num)', 'PersonaController::form/$1'); // Editar persona
$routes->post('/personas/guardar', 'PersonaController::guardar');
$routes->post('persona/eliminar', 'PersonaController::eliminar');
$routes->get('ubicacion/provincias/(:num)', 'UbicacionController::getProvincias/$1');
$routes->get('ubicacion/distritos/(:num)', 'UbicacionController::getDistritos/$1');


$routes->get('usuarios', 'UsuarioController::index');      // Listado de usuarios
$routes->get('usuarios/crear', 'UsuarioController::crear'); // Formulario para crear usuario
$routes->post('usuarios/guardar', 'UsuarioController::guardar'); // Guardar usuario

/* Leads */
$routes->get('lead/kanban', 'LeadController::kanban');
$routes->get('leads', 'LeadController::kanban'); 
$routes->get('lead/crear', 'LeadController::crear');
$routes->post('lead/guardar', 'LeadController::guardar');
$routes->get('lead/detalle/(:num)', 'LeadController::detalle/$1');
$routes->post('lead/eliminar', 'LeadController::eliminar');
$routes->post('lead/avanzarEtapa', 'LeadController::avanzarEtapa');
$routes->get('persona/convertirALead/(:num)', 'PersonaController::convertirALead/$1');


$routes->get('api/personas/buscardni/(:num)', 'PersonaController::BuscadorDni/$1');




$routes->get('lead/pruebaLeadCompleto', 'LeadController::pruebaLeadCompleto');



