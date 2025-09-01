<?php

namespace App\Controllers;

class PersonaController extends BaseController
{
  public function index(): string
  {
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');
    return view('Personas/index', $datos);
  }
}
