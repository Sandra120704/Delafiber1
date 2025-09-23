<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

/**
 * Controlador para manejar el proceso de autenticación de usuarios
 */
class LoginController extends BaseController
{
    /**
     * Muestra la página de inicio de sesión
     */
    public function index()
    {
        // Cargar la vista del formulario de login
        return view('login/login');
    }

    /**
     * Procesa la autenticación del usuario
     */
    public function auth()
    {
        // Obtener los datos del formulario de login
        $nombreUsuario = $this->request->getPost('usuario');
        $password = $this->request->getPost('clave');

        // Crear instancia del modelo de usuarios
        $modeloUsuario = new UsuarioModel();
        
        // Buscar el usuario en la base de datos con las credenciales proporcionadas
        $datosUsuario = $modeloUsuario->where('usuario', $nombreUsuario)
                                     ->where('clave', $password) 
                                     ->first();

        // Verificar si se encontró el usuario
        if($datosUsuario) {
            // Usuario encontrado - crear sesión
            $sesion = session();
            
            // Guardar información importante en la sesión
            $sesion->set([
                'idusuario' => $datosUsuario['idusuario'],  // ID del usuario
                'usuario'   => $datosUsuario['usuario'],    // Nombre de usuario
                'rol'       => $datosUsuario['idrol'],      // Rol del usuario
                'isLoggedIn'=> true                         // Marcar como autenticado
            ]);

            // Redirigir al dashboard principal después del login exitoso
            return redirect()->to('/dashboard'); 
        } else {
            // Usuario no encontrado - mostrar error y regresar al login
            return redirect()->back()->with('error', 'Usuario o contraseña incorrectos.');
        }
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout()
    {
        // Destruir toda la información de la sesión
        session()->destroy();
        
        // Redirigir de vuelta a la página de login
        return redirect()->to('login'); 
    }
}
