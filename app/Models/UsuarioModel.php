<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para manejar todos los datos de usuarios del sistema
 * Incluye autenticación, perfiles y estadísticas de rendimiento
 */
class UsuarioModel extends Model
{
    // === CONFIGURACIÓN DE LA TABLA ===
    protected $table = 'usuarios';                    // Nombre de la tabla en la BD
    protected $primaryKey = 'idusuario';              // Campo clave primaria
    protected $allowedFields = ['usuario', 'clave', 'idrol', 'idpersona', 'activo']; // Campos que se pueden modificar
    protected $useTimestamps = false;                 // No usar timestamps automáticos
    
    protected $conexionBD;  // Variable para manejar conexiones personalizadas

    /**
     * Obtiene lista completa de usuarios con información detallada
     * Incluye nombre de la persona, rol, estadísticas, etc.
     */
    public function getUsuariosConDetalle()
    {
        try {
            // Usar Query Builder del modelo para la consulta completa
            return $this->select('
                usuarios.idusuario,
                usuarios.usuario as nombreUsuario,
                usuarios.clave,
                COALESCE(usuarios.activo, 1) as estadoActivo,
                usuarios.idrol,
                usuarios.idpersona,
                COALESCE(CONCAT(personas.nombres, " ", personas.apellidos), "Sin asignar") as nombrePersona,
                personas.correo as emailPersona,
                personas.telefono,
                roles.nombre as nombreRol,
                roles.descripcion as descripcionRol,
                0 as totalLeads,
                0 as totalTareas,
                0 as tasaConversion
            ')
            ->join('personas', 'usuarios.idpersona = personas.idpersona', 'left')
            ->join('roles', 'usuarios.idrol = roles.idrol', 'left')
            ->orderBy('usuarios.idusuario')
            ->findAll();
            
        } catch (\Exception $error) {
            // Si hay error en la consulta compleja, usar método simple
            return $this->getUsuariosBasico();
        }
    }
    
    /**
     * Método de respaldo - Obtiene usuarios con información básica
     * Se usa cuando falla la consulta principal
     */
    public function getUsuariosBasico()
    {
        // Obtener todos los usuarios de forma simple
        $listaUsuarios = $this->findAll();
        
        // Agregar campos faltantes con valores por defecto
        foreach ($listaUsuarios as &$datosUsuario) {
            $datosUsuario['nombreUsuario'] = $datosUsuario['usuario'] ?? '';
            $datosUsuario['nombrePersona'] = 'Usuario ID: ' . $datosUsuario['idusuario'];
            $datosUsuario['nombreRol'] = 'Sin rol asignado';
            $datosUsuario['estadoActivo'] = $datosUsuario['activo'] ?? 1;
            $datosUsuario['emailPersona'] = '';
            $datosUsuario['telefono'] = '';
            $datosUsuario['totalLeads'] = 0;
            $datosUsuario['totalTareas'] = 0;
            $datosUsuario['tasaConversion'] = 0;
        }
        
        return $listaUsuarios;
    }
    
    /**
     * Obtiene usuarios con nombres completos de las personas
     * Método optimizado para mostrar listas simples
     */
    public function obtenerUsuariosConNombres()
    {
        // Usar el Query Builder del modelo directamente
        return $this->select('usuarios.*, CONCAT(personas.nombres, " ", personas.apellidos) as nombreCompleto')
                    ->join('personas', 'usuarios.idpersona = personas.idpersona', 'left')
                    ->findAll();
    }
    
    /**
     * Obtiene información completa de un usuario específico
     * Incluye todos los datos personales y del rol
     */
    public function obtenerUsuarioCompleto($idUsuario)
    {
        // Usar Query Builder del modelo directamente
        return $this->select('
            usuarios.*,                                              
            CONCAT(personas.nombres, " ", personas.apellidos) as nombrePersona,  
            personas.correo, personas.telefono, personas.direccion,                
            roles.nombre as nombreRol                             
        ')
        ->join('personas', 'usuarios.idpersona = personas.idpersona', 'left')    
        ->join('roles', 'usuarios.idrol = roles.idrol', 'left')               
        ->find($idUsuario);                                          
    }
}