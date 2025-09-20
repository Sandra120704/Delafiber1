<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'idusuario';
    protected $allowedFields = ['usuario', 'clave', 'idrol', 'idpersona', 'activo'];
    protected $useTimestamps = false; 
    protected $db;
    public function getUsuariosConDetalle()
    {
        // Primero prueba una query simple
       $db = Database::connect();
        
        $query = "
            SELECT 
                u.idusuario,
                u.usuario as username,
                u.clave,
                COALESCE(u.activo, 1) as activo,
                u.idrol,
                u.idpersona,
                COALESCE(CONCAT(p.nombres, ' ', p.apellidos), 'Sin asignar') as nombre_persona,
                p.correo as email,
                p.telefono,
                r.nombre as nombre_rol,
                r.descripcion as descripcion_rol,
                0 as total_leads,
                0 as total_tareas,
                0 as conversion_rate
            FROM usuarios u
            LEFT JOIN personas p ON u.idpersona = p.idpersona
            LEFT JOIN roles r ON u.idrol = r.idrol
            ORDER BY u.idusuario
        ";
        
        try {
            return $db->query($query)->getResultArray();
        } catch (\Exception $e) {
            // Si falla, usar método básico sin joins
            log_message('error', 'Error en getUsuariosConDetalle: ' . $e->getMessage());
            return $this->getUsuariosBasico();
        }
    }
    
    public function getUsuariosBasico()
    {
        $usuarios = $this->findAll();
        
        // se Agrega campos faltantes con valores por defecto
        foreach ($usuarios as &$usuario) {
            $usuario['username'] = $usuario['usuario'] ?? '';
            $usuario['nombre_persona'] = 'Usuario ID: ' . $usuario['idusuario'];
            $usuario['nombre_rol'] = 'Sin rol';
            $usuario['activo'] = $usuario['activo'] ?? 1;
            $usuario['email'] = '';
            $usuario['telefono'] = '';
            $usuario['total_leads'] = 0;
            $usuario['total_tareas'] = 0;
            $usuario['conversion_rate'] = 0;
        }
        
        return $usuarios;
    }
    
    // Método  actualizar
    public function obtenerUsuariosConNombres()
    {
        return $this->select('u.*, CONCAT(p.nombres, " ", p.apellidos) as nombre_completo')
                    ->join('personas p', 'u.idpersona = p.idpersona', 'left')
                    ->findAll();
    }
    
    // Método obtener usuario con detalles
    public function obtenerUsuarioCompleto($id)
    {
        return $this->select('
            u.*,
            CONCAT(p.nombres, " ", p.apellidos) as nombre_persona,
            p.correo, p.telefono, p.direccion,
            r.nombre as rol_nombre
        ')
        ->join('personas p', 'u.idpersona = p.idpersona', 'left')
        ->join('roles r', 'u.idrol = r.idrol', 'left')
        ->find($id);
    }
}