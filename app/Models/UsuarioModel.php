<?php
namespace App\Models;
use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'idusuario';
    protected $allowedFields = [
      'idpersona',
      'username', 
      'password', 
      'idrol', 
      'activo', 
      'creado', 
      'modificado'
    ];

    // Obtener usuarios con join a personas y roles
    public function getUsuarios()
    {
        return $this->select('usuarios.idusuario, personas.nombres as nombre_persona, username, roles.nombre as nombre_rol, usuarios.activo')
                    ->join('personas', 'usuarios.idpersona = personas.idpersona')
                    ->join('roles', 'usuarios.idrol = roles.idrol')
                    ->findAll();
    }
}
