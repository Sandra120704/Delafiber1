<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadHistorialModel extends Model
{
    protected $table = 'leads_historial';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'idlead',
        'idusuario',
        'accion',
        'descripcion',
        'etapa_anterior',
        'etapa_nueva',
        'fecha',
        'datos_adicionales' // JSON para información extra
    ];
    
    protected $useTimestamps = false; // Usamos campo fecha personalizado
    
    protected $validationRules = [
        'idlead' => 'required|integer',
        'idusuario' => 'required|integer',
        'accion' => 'required|max_length[50]',
        'descripcion' => 'required|max_length[500]'
    ];
    
    protected $validationMessages = [
        'idlead' => [
            'required' => 'El ID del lead es obligatorio',
            'integer' => 'El ID del lead debe ser un número'
        ],
        'idusuario' => [
            'required' => 'El ID del usuario es obligatorio',
            'integer' => 'El ID del usuario debe ser un número'
        ],
        'accion' => [
            'required' => 'La acción es obligatoria',
            'max_length' => 'La acción no puede exceder 50 caracteres'
        ],
        'descripcion' => [
            'required' => 'La descripción es obligatoria',
            'max_length' => 'La descripción no puede exceder 500 caracteres'
        ]
    ];
    
    protected $skipValidation = false;

    // Obtener historial completo de un lead con información de usuario
    public function getHistorialCompleto($idlead)
    {
        return $this->select('leads_historial.*, usuarios.nombre as usuario_nombre')
                   ->join('usuarios', 'usuarios.idusuario = leads_historial.idusuario', 'left')
                   ->where('idlead', $idlead)
                   ->orderBy('fecha', 'DESC')
                   ->findAll();
    }
    
    // Registrar una acción específica
    public function registrarAccion($idlead, $accion, $descripcion, $datosAdicionales = null)
    {
        $data = [
            'idlead' => $idlead,
            'idusuario' => session()->get('idusuario') ?? 1,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'fecha' => date('Y-m-d H:i:s'),
            'datos_adicionales' => $datosAdicionales ? json_encode($datosAdicionales) : null
        ];
        
        return $this->insert($data);
    }
}