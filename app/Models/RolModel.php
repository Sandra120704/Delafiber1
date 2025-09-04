<?php
namespace App\Models;
use CodeIgniter\Model;

class RolModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'idrol';
    protected $allowedFields = ['nombre', 'descripcion'];

    // Obtener todos los roles
    public function getRoles()
    {
        return $this->findAll();
    }

    // Obtener un rol por ID
    public function getRol($idrol)
    {
        return $this->where('idrol', $idrol)->first();
    }
}
