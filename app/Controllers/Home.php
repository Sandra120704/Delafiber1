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
    public function index(): string
    {
        // Preparar los datos para la página de bienvenida
        $datosVista = [
            'header' => view('Layouts/header'),  // Barra de navegación superior
            'footer' => view('Layouts/footer')   // Pie de página con scripts
        ];
        
        // Cargar la página de bienvenida con header y footer incluidos
        return view('welcome', $datosVista);
    }
}
