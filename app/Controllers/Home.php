<?php

namespace App\Controllers;

/**
 * Controlador de la página principal/bienvenida
 * Este es el primer controlador que se carga cuando alguien visita el sitio
 */
class Home extends BaseController
{
    /**
     * Página principal del sistema
     * Muestra una página de bienvenida con los elementos básicos
     */
    public function index()
    {
        return redirect()->to(base_url('dashboard'));
    }
}
