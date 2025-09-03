<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('personas', 'PersonaController::index');
/* Rutas Campañas */
$routes->get('campanas', 'CampanaControllers::index');        // Listado
$routes->get('campanas', 'CampanaControllers::index');          // Listado
$routes->post('campanas/crear', 'CampanaControllers::crear'); 

/* Rutas De Direccion */
$routes->get('campanas', 'CampanaControllers::index');        // Mostrar listado + formulario
$routes->post('campanas/crear', 'CampanaControllers::crear');

/* Rutas Registrar Persona */
// Personas
/* Personas */
$routes->get('personas', 'PersonaController::index');  // Listado
$routes->get('persona/form', 'PersonaController::form'); // Crear nueva persona
$routes->get('persona/form/(:num)', 'PersonaController::form/$1'); // Editar persona
$routes->post('persona/guardar', 'PersonaController::guardar');
$routes->post('persona/eliminar', 'PersonaController::eliminar');

$routes->get('persona/getProvincias/(:num)', 'PersonaController::getProvincias/$1');
$routes->get('persona/getDistritos/(:num)', 'PersonaController::getDistritos/$1');





