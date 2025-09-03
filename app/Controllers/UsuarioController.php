<?php

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
class UsuarioController extends BaseController{
  public function login(){
    return view('usuario/login');
  }
  public function dologin(){
    $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $usuarioModel = new UsuarioModel();
        $user = $usuarioModel->getByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            session()->set('usuario', $user);
            return redirect()->to('/dashboard');
        }

        return redirect()->back()->with('error','Usuario o contraseña incorrectos');
  }
    public function logout() {
        session()->destroy();
        return redirect()->to('/login');
    }
}