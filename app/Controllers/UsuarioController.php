<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PersonaModel;
use App\Models\RolesModel; 
use Config\Database;

class UsuarioController extends BaseController
{
    protected $usuarioModel;
    protected $personaModel;
    protected $rolesModel;
    protected $db;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->personaModel = new PersonaModel();
        $this->rolesModel = new RolesModel();
        $this->db           = Database::connect();
    }

    public function index()
    {
        try {
            $usuarios = $this->usuarioModel->getUsuariosConDetalle();
            
            $data = [
                'header' => view('layouts/header'),
                'footer' => view('layouts/footer'),
                'usuarios' => $usuarios,
                'personas' => $this->personaModel->findAll(),
                'roles' => $this->rolesModel->findAll()
            ];

            return view('usuarios/index', $data);
            
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            echo "<br>Probando query básica:";
            
            $db = Database::connect();
            $query = $db->query("SELECT idusuario, usuario FROM usuarios LIMIT 3");
            $resultados = $query->getResultArray();
            
            echo "<pre>";
            var_dump($resultados);
            echo "</pre>";
            
            die();
        }
    }

    public function crear()
    {
        if ($this->request->getMethod() === 'POST') {
            return $this->guardar();
        }

        $data = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'personas' => $this->personaModel->findAll(),
            'roles' => $this->rolesModel->findAll()
        ];
        
        return view('usuarios/crear', $data);
    }

    public function guardar()
    {
        $rules = [
            'usuario' => 'required|min_length[4]|is_unique[usuarios.usuario]',
            'clave' => 'required|min_length[6]', 
            'idpersona' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $data = [
                'idpersona' => $this->request->getVar('idpersona') ?: null,
                'usuario' => $this->request->getVar('usuario'), // Cambié username
                'clave' => password_hash($this->request->getVar('clave'), PASSWORD_DEFAULT), // Cambié password
                'idrol' => $this->request->getVar('idrol'),
                'activo' => $this->request->getVar('activo') ? 1 : 0
            ];

            $id = $this->usuarioModel->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'idusuario' => $id
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al crear el usuario: ' . $e->getMessage()
            ]);
        }
    }

    public function editar($idusuario)
    {
        if ($this->request->getMethod() === 'POST') {
            return $this->actualizar($idusuario);
        }

        $usuario = $this->usuarioModel->obtenerUsuarioCompleto($idusuario);
        
        if (!$usuario) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Usuario no encontrado');
        }

        $data = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'usuario' => $usuario,
            'personas' => $this->personaModel->findAll(),
            'roles' => $this->rolesModel->findAll()
        ];

        return view('usuarios/editar', $data);
    }

    public function actualizar($idusuario)
    {
        $usuario = $this->usuarioModel->find($idusuario);
        if (!$usuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
        }

        $rules = [
            'usuario' => "required|min_length[4]|is_unique[usuarios.usuario,idusuario,{$idusuario}]",
            'idrol' => 'required|integer',
            'idpersona' => 'permit_empty|integer'
        ];

        if ($this->request->getVar('clave')) {
            $rules['clave'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $data = [
                'idpersona' => $this->request->getVar('idpersona') ?: null,
                'usuario' => $this->request->getVar('usuario'),
                'idrol' => $this->request->getVar('idrol'),
                'activo' => $this->request->getVar('activo') ? 1 : 0
            ];

            if ($this->request->getVar('clave')) {
                $data['clave'] = password_hash($this->request->getVar('clave'), PASSWORD_DEFAULT);
            }

            $this->usuarioModel->update($idusuario, $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Usuario actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminar($idusuario)
    {
        try {
            $usuario = $this->usuarioModel->obtenerUsuarioCompleto($idusuario);
            
            if (!$usuario) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }

            // No permitir eliminar administradores
            if ($usuario['rol_nombre'] === 'admin') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se puede eliminar un administrador'
                ]);
            }

            // Verificar si tiene leads o tareas asignadas
            $tieneLeads = $this->db->table('leads')->where('idusuario', $idusuario)->countAllResults();
            $tieneTareas = $this->db->table('tareas')->where('idusuario', $idusuario)->countAllResults();

            if ($tieneLeads > 0 || $tieneTareas > 0) {
                // En lugar de eliminar, desactivar
                $this->usuarioModel->update($idusuario, ['activo' => 0]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Usuario desactivado (tenía leads/tareas asignadas)'
                ]);
            }

            $this->usuarioModel->delete($idusuario);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
            ]);
        }
    }

    public function cambiarEstado($idusuario)
    {
        $activo = $this->request->getVar('activo');
        
        try {
            $this->usuarioModel->update($idusuario, ['activo' => $activo]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ]);
        }
    }

    public function resetearPassword($idusuario) 
    {
        $nuevaPassword = $this->request->getVar('nueva_password');
        
        if (!$nuevaPassword || strlen($nuevaPassword) < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ]);
        }

        try {
            $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
            $this->usuarioModel->update($idusuario, ['clave' => $passwordHash]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar la contraseña: ' . $e->getMessage()
            ]);
        }
    }

    public function verPerfil($idusuario)
    {
        $usuario = $this->usuarioModel->obtenerUsuarioCompleto($idusuario);
        
        if (!$usuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
        }

        // Obtener estadísticas adicionales
        $db = Database::connect();
        
        $estadisticas = [
            'leads_mes_actual' => $db->table('leads')
                ->where('idusuario', $idusuario)
                ->where('MONTH(fecha_registro)', date('m'))
                ->where('YEAR(fecha_registro)', date('Y'))
                ->countAllResults(),
            'tareas_pendientes' => $db->table('tareas')
                ->where('idusuario', $idusuario)
                ->where('estado', 'Pendiente')
                ->countAllResults(),
            'ultima_actividad' => $db->table('leads')
                ->select('fecha_registro')
                ->where('idusuario', $idusuario)
                ->orderBy('fecha_registro', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray()
        ];

        return $this->response->setJSON([
            'success' => true,
            'usuario' => $usuario,
            'estadisticas' => $estadisticas
        ]);
    }
}