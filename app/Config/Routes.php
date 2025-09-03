<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('personas', 'PersonaController::index');


$routes->get('campanas', 'CampanaController::index');          // listado
$routes->get('campana/form', 'CampanaController::form');       // crear
$routes->get('campana/form/(:num)', 'CampanaController::form/$1'); // editar
$routes->post('campana/guardar', 'CampanaController::guardar');    // guardar
$routes->post('campana/eliminar', 'CampanaController::eliminar');  // eliminar

/* Personas */
$routes->get('personas', 'PersonaController::index');  // Listado
$routes->get('persona/form', 'PersonaController::form'); // Crear nueva persona
$routes->get('persona/form/(:num)', 'PersonaController::form/$1'); // Editar persona
$routes->post('persona/guardar', 'PersonaController::guardar');
$routes->post('persona/eliminar', 'PersonaController::eliminar');

$routes->get('persona/getProvincias/(:num)', 'PersonaController::getProvincias/$1');
$routes->get('persona/getDistritos/(:num)', 'PersonaController::getDistritos/$1');





