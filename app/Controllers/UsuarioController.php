<?php
namespace App\Controllers;
use App\Models\UsuarioModel;
use App\Models\PersonaModel;
use App\Models\RolModel;

class UsuarioController extends BaseController
{
    protected $usuarioModel;
    protected $personaModel;
    protected $rolModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->personaModel = new PersonaModel();
        $this->rolModel = new RolModel();
    }

    public function index()
    {
      $data = [
      'header' => view('Layouts/header'),
      'footer' => view('Layouts/footer'),
      'usuarios' => $this->usuarioModel->getUsuariosConDetalle()
  ];

    return view('usuarios/index', $data);

    }

    public function crear()
    {
        $personas = $this->personaModel->findAll();
        $roles = $this->rolModel->findAll();
        
        return view('usuarios/crear', ['personas' => $personas, 'roles' => $roles]);
    }

    public function guardar()
    {
        $data = [
            'idpersona' => $this->request->getPost('idpersona'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'idrol' => $this->request->getPost('idrol'),
            'activo' => 1
        ];

        $usuarioModel = $this->usuarioModel;
        $insert = $usuarioModel->insert($data);

        if ($insert) {
            return $this->response->setJSON([
                'success' => true,
                'mensaje' => 'Usuario creado correctamente'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'mensaje' => 'Error al crear el usuario'
            ]);
        }
    }
    public function editar($idusuario)
    {
        $usuario = $this->usuarioModel->find($idusuario);
        $personas = $this->personaModel->findAll();
        $roles = $this->rolModel->findAll();

        return view('usuarios/editar', [
            'usuario' => $usuario,
            'personas' => $personas,
            'roles' => $roles
        ]);
    }
    

     public function actualizar()
    {
        $idusuario = $this->request->getPost('idusuario');
        $data = [
            'idpersona' => $this->request->getPost('idpersona'),
            'username' => $this->request->getPost('username'),
            'idrol' => $this->request->getPost('idrol'),
            'activo' => $this->request->getPost('activo') ? 1 : 0
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->usuarioModel->update($idusuario, $data);

        return $this->response->setJSON([
            'success' => true,
            'mensaje' => 'Usuario actualizado correctamente'
        ]);
    }
}
