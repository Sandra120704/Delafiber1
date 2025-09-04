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
$routes->get('persona/form', 'PersonaController::form'); // Crear nueva persona
$routes->get('persona/form/(:num)', 'PersonaController::form/$1'); // Editar persona
$routes->post('persona/guardar', 'PersonaController::guardar');
$routes->post('persona/eliminar', 'PersonaController::eliminar');

$routes->get('persona/getProvincias/(:num)', 'PersonaController::getProvincias/$1');
$routes->get('persona/getDistritos/(:num)', 'PersonaController::getDistritos/$1');

$routes->get('usuarios', 'UsuarioController::index');      // Listado de usuarios
$routes->get('usuarios/crear', 'UsuarioController::crear'); // Formulario para crear usuario
$routes->post('usuarios/guardar', 'UsuarioController::guardar'); // Guardar usuario

/* Leads */
/* Leads */
$routes->get('lead/kanban', 'LeadController::kanban');        // Vista Kanban
$routes->get('leads/detalle/(:num)', 'LeadController::detalle/$1'); // Modal detalle lead
 
$routes->post('leads/avanzar-etapa', 'LeadController::avanzarEtapa');
$routes->post('leads/seguimiento', 'LeadController::guardarSeguimiento');
$routes->post('leads/tarea', 'LeadController::guardarTarea');

// Leads
$routes->get('lead/crear', 'LeadController::crear');   // para abrir modal
$routes->post('lead/guardar', 'LeadController::guardar'); // para guardar Lead
$routes->get('lead/kanban', 'LeadController::kanban'); 








