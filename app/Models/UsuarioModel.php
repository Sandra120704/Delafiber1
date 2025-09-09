<?php

namespace App\Models;
use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'idusuario';
    protected $allowedFields = ['usuario','clave','idrol','idpersona','activo'];

    public function getUsuariosConDetalle()
    {
        return $this->select('usuarios.idusuario, usuarios.usuario AS username, personas.nombres AS nombre_persona, roles.nombre AS nombre_rol, usuarios.activo')
                    ->join('personas', 'personas.idpersona = usuarios.idpersona')
                    ->join('roles', 'roles.idrol = usuarios.idrol')
                    ->findAll();
    }

}

