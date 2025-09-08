<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class LoginController extends BaseController
{
    public function index()
    {
        return view('login/login'); // Asegúrate que la vista exista
    }

    public function auth()
    {
        $usuario = $this->request->getPost('usuario');
        $clave   = $this->request->getPost('clave');

        $model = new UsuarioModel();
        $user = $model->where('usuario', $usuario)
                      ->where('clave', $clave) // temporal, luego usar hash
                      ->first();

        if($user) {
            // Guardar sesión
            $session = session();
            $session->set([
                'idusuario' => $user['idusuario'],
                'usuario'   => $user['usuario'],
                'rol'       => $user['idrol'],
                'isLoggedIn'=> true
            ]);

            // Redirige a la página principal
            return redirect()->to('/'); 
        } else {
            return redirect()->back()->with('error', 'Usuario o contraseña incorrectos.');
        }
    }

    public function logout()
    {
        session()->destroy();  // destruye la sesión del usuario
        return redirect()->to('login');  // redirige al login
    }
}
