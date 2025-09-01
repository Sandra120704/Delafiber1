<?php

namespace App\Controllers;
use App\Models\PersonaModel;

class PersonaController extends BaseController
{
  public function index(): string
  {
    $personaModel = new PersonaModel();
    $personas = $personaModel -> obtenerPersona();

    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');
    $datos['personas'] = $personas;
    
    return view('Personas/index', $datos);
  }
}
