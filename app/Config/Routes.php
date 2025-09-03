<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('personas', 'PersonaController::index');
/* Rutas Campañas */
$routes->get('campanas', 'CampanaControllers::index');        // Listado
$routes->get('campanas/crear', 'CampanaControllers::crear');  // Formulario
$routes->post('campanas/crear', 'CampanaControllers::crear'); 

/* Rutas De Direccion */
$routes->get('persona/getProvincias/(:num)', 'PersonaController::getProvincias/$1');
$routes->get('persona/getDistritos/(:num)', 'PersonaController::getDistritos/$1');

/* Rutas Registrar Persona */
$routes->get('personas', 'PersonaController::index'); // lista de personas
$routes->get('persona/form', 'PersonaController::form'); // formulario para AJAX
$routes->post('persona/guardar', 'PersonaController::guardar'); // guardar nueva persona

// Opcionales para cascada
$routes->get('persona/provincias/(:num)', 'PersonaController::getProvincias/$1');
$routes->get('persona/distritos/(:num)', 'PersonaController::getDistritos/$1');

