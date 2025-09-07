<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home
$routes->get('/', 'Home::index');

// ============================
// PERSONAS
// ============================
$routes->get('personas', 'PersonaController::index');               // Listado
$routes->get('personas/crear', 'PersonaController::crear');         // Crear persona
$routes->get('personas/editar/(:num)', 'PersonaController::form/$1'); // Editar persona
$routes->post('personas/guardar', 'PersonaController::guardar');   // Guardar persona
$routes->post('personas/eliminar', 'PersonaController::eliminar'); // Eliminar persona
$routes->get('persona/convertir-a-lead/(:num)', 'PersonaController::convertirALead/$1'); // Convertir persona a lead

// API para búsqueda DNI
$routes->get('api/personas/buscardni/(:num)', 'PersonaController::BuscadorDni/$1');

// ============================
// CAMPANAS
// ============================
$routes->get('campanas', 'CampanaController::index');               // Listado
$routes->get('campana/crear', 'CampanaController::crear');
$routes->get('campana/form/(:num)', 'CampanaController::form/$1'); // para editar
$routes->post('campana/guardar', 'CampanaController::guardar');
  
$routes->post('campanas/eliminar', 'CampanaController::eliminar');  
$routes->post('campanas/cambiar-estado', 'CampanaController::cambiarEstado');

// ============================
// USUARIOS
// ============================
$routes->get('usuarios', 'UsuarioController::index');               // Listado
$routes->get('usuarios/crear', 'UsuarioController::crear');         // Crear usuario
$routes->post('usuarios/guardar', 'UsuarioController::guardar');   // Guardar usuario

// ============================
// LEADS
// ============================
$routes->get('leads/kanban', 'LeadController::kanban');             // Vista Kanban
$routes->get('leads/crear', 'LeadController::crear');               // Crear lead
$routes->post('leads/guardar', 'LeadController::guardar');         // Guardar lead
$routes->get('leads/detalle/(:num)', 'LeadController::detalle/$1'); // Detalle de lead
$routes->post('leads/eliminar', 'LeadController::eliminar');       
$routes->post('leads/avanzar-etapa', 'LeadController::avanzarEtapa'); 
$routes->get('leads/prueba-completo', 'LeadController::pruebaLeadCompleto');

// ============================
// UBICACION (Departamentos / Provincias / Distritos)
// ============================
$routes->get('ubicacion/provincias/(:num)', 'UbicacionController::getProvincias/$1');
$routes->get('ubicacion/distritos/(:num)', 'UbicacionController::getDistritos/$1');
