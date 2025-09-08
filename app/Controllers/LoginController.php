<?php
namespace App\Controllers;

use App\Models\UsuarioModel;

class LoginController extends BaseController
{
    public function index()
    {
        return view('login'); // Vista del formulario
    }

    public function auth()
    {
        $usuario = $this->request->getPost('usuario');
        $clave   = $this->request->getPost('clave');

        $usuarioModel = new UsuarioModel();
        $user = $usuarioModel->getByUsuario($usuario);

        if ($user && $user['activo'] == 1) {
            //si aún guardas la clave en texto plano, cámbiala a password_hash
            if ($clave === $user['clave'] || password_verify($clave, $user['clave'])) {
                session()->set([
                    'idusuario' => $user['idusuario'],
                    'usuario'   => $user['usuario'],
                    'idrol'     => $user['idrol'],
                    'logged_in' => true
                ]);
                return redirect()->to('/dashboard');
            }
        }

        return redirect()->back()->with('error', 'Usuario o clave incorrectos');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
