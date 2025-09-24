<?php

namespace App\Models;
use CodeIgniter\Model;

class CampanaModel extends Model
{
    protected $table = 'campanias';
    protected $primaryKey = 'idcampania';
    
    protected $allowedFields = [
        'nombre', 
        'descripcion', 
        'fecha_inicio', 
        'fecha_fin', 
        'presupuesto', 
        'estado',
        'responsable'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[150]|alpha_numeric_punct',
        'descripcion' => 'permit_empty|max_length[1000]',
        'presupuesto' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than[99999999.99]',
        'fecha_inicio' => 'permit_empty|valid_date',
        'fecha_fin' => 'permit_empty|valid_date',
        'estado' => 'permit_empty|in_list[Activa,Inactiva]',
        'responsable' => 'permit_empty|integer'
    ];
    
    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre de la campaña es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede exceder 150 caracteres.',
            'alpha_numeric_punct' => 'El nombre solo puede contener letras, números y signos de puntuación.'
        ],
        'presupuesto' => [
            'decimal' => 'El presupuesto debe ser un número válido.',
            'greater_than_equal_to' => 'El presupuesto no puede ser negativo.',
            'less_than' => 'El presupuesto no puede exceder 99,999,999.99.'
        ],
        'estado' => [
            'in_list' => 'El estado debe ser Activa o Inactiva.'
        ]
    ];

    public function getAllWithDetails()
    {
        return $this->select('
                campanias.*, 
                CONCAT(p.nombres, " ", p.apellidos) as responsable_nombre,
                u.usuario as responsable_usuario,
                COALESCE(SUM(d.presupuesto), 0) as inversion_total,
                COALESCE(SUM(d.leads_generados), 0) as leads_total
            ')
            ->join('usuarios u', 'u.idusuario = campanias.responsable', 'left')
            ->join('personas p', 'p.idpersona = u.idpersona', 'left')
            ->join('difusiones d', 'd.idcampania = campanias.idcampania', 'left')
            ->groupBy('campanias.idcampania')
            ->orderBy('campanias.idcampania', 'DESC')
            ->findAll();
    }

    public function getCampanasFiltered($filtros = [])
    {
        $builder = $this->select('
                campanias.*, 
                CONCAT(p.nombres, " ", p.apellidos) as responsable_nombre,
                u.usuario as responsable_usuario,
                COALESCE(SUM(d.presupuesto), 0) as inversion_total,
                COALESCE(SUM(d.leads_generados), 0) as leads_total
            ')
            ->join('usuarios u', 'u.idusuario = campanias.responsable', 'left')
            ->join('personas p', 'p.idpersona = u.idpersona', 'left')
            ->join('difusiones d', 'd.idcampania = campanias.idcampania', 'left');

        if (!empty($filtros['search'])) {
            $builder->groupStart()
                ->like('campanias.nombre', $filtros['search'])
                ->orLike('campanias.descripcion', $filtros['search'])
                ->groupEnd();
        }

        if (!empty($filtros['estado'])) {
            $builder->where('campanias.estado', $filtros['estado']);
        }

        if (!empty($filtros['responsable'])) {
            $builder->where('campanias.responsable', $filtros['responsable']);
        }

        if (!empty($filtros['presupuesto_min'])) {
            $builder->where('campanias.presupuesto >=', $filtros['presupuesto_min']);
        }

        if (!empty($filtros['presupuesto_max'])) {
            $builder->where('campanias.presupuesto <=', $filtros['presupuesto_max']);
        }

        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('campanias.fecha_inicio >=', $filtros['fecha_inicio']);
        }

        if (!empty($filtros['fecha_fin'])) {
            $builder->where('campanias.fecha_fin <=', $filtros['fecha_fin']);
        }

        return $builder->groupBy('campanias.idcampania')
            ->orderBy('campanias.idcampania', 'DESC')
            ->findAll();
    }

    public function getDetalle($idcampania)
    {
        $campana = $this->select('
                campanias.*, 
                CONCAT(p.nombres, " ", p.apellidos) as responsable_nombre,
                u.usuario as responsable_usuario
            ')
            ->join('usuarios u', 'u.idusuario = campanias.responsable', 'left')
            ->join('personas p', 'p.idpersona = u.idpersona', 'left')
            ->find($idcampania);

        if ($campana) {
            $metricas = $this->getMetricasCampana($idcampania);
            $campana = array_merge($campana, $metricas);
        }

        return $campana;
    }


    public function getMetricas()
    {
        // Total de campañas
        $total_campanas = $this->countAllResults(false);
        
        // Campañas por estado
        $activas = $this->where('estado', 'Activa')->countAllResults(false);
        $inactivas = $this->where('estado', 'Inactiva')->countAllResults(false);

        // Presupuesto total
        $presupuesto_result = $this->selectSum('presupuesto')->first();
        $presupuesto_total = $presupuesto_result['presupuesto'] ?? 0;

        // Total gastado en difusiones
        $gastado_result = $this->db->table('difusiones')
            ->selectSum('presupuesto')
            ->get()
            ->getRow();
        $presupuesto_gastado = $gastado_result->presupuesto ?? 0;

        // Total de leads
        $leads_result = $this->db->table('difusiones')
            ->selectSum('leads_generados')
            ->get()
            ->getRow();
        $total_leads = $leads_result->leads_generados ?? 0;

        // ROI promedio
        $roi_promedio = $presupuesto_gastado > 0 ? 
            round(($total_leads * 100) / $presupuesto_gastado, 2) : 0;

        return [
            'total_campanas' => $total_campanas,
            'activas' => $activas,
            'inactivas' => $inactivas,
            'presupuesto_total' => $presupuesto_total,
            'presupuesto_gastado' => $presupuesto_gastado,
            'presupuesto_disponible' => $presupuesto_total - $presupuesto_gastado,
            'porcentaje_gastado' => $presupuesto_total > 0 ? 
                round(($presupuesto_gastado / $presupuesto_total) * 100, 1) : 0,
            'total_leads' => $total_leads,
            'roi_promedio' => $roi_promedio,
            'costo_por_lead' => $total_leads > 0 ? 
                round($presupuesto_gastado / $total_leads, 2) : 0
        ];
    }

    public function getMetricasCampana($idcampania)
    {
        $result = $this->db->table('difusiones')
            ->select('
                COALESCE(SUM(presupuesto), 0) as inversion_total,
                COALESCE(SUM(leads_generados), 0) as leads_total,
                COUNT(*) as medios_count
            ')
            ->where('idcampania', $idcampania)
            ->get()
            ->getRow();

        if (!$result) {
            return [
                'inversion_total' => 0,
                'leads_total' => 0,
                'medios_count' => 0,
                'roi' => 0,
                'costo_por_lead' => 0
            ];
        }

        $roi = $result->inversion_total > 0 ? 
            round(($result->leads_total * 100) / $result->inversion_total, 2) : 0;

        $costo_por_lead = $result->leads_total > 0 ? 
            round($result->inversion_total / $result->leads_total, 2) : 0;

        return [
            'inversion_total' => $result->inversion_total,
            'leads_total' => $result->leads_total,
            'medios_count' => $result->medios_count,
            'roi' => $roi,
            'costo_por_lead' => $costo_por_lead
        ];
    }

    public function getRendimientoMensual()
    {
        $resultado = $this->db->query("
            SELECT 
                DATE_FORMAT(c.fecha_inicio, '%Y-%m') as mes,
                COUNT(c.idcampania) as campanas,
                COALESCE(SUM(d.leads_generados), 0) as leads,
                COALESCE(SUM(d.presupuesto), 0) as inversion,
                CASE 
                    WHEN SUM(d.presupuesto) > 0 
                    THEN ROUND(SUM(d.leads_generados) / SUM(d.presupuesto) * 100, 2)
                    ELSE 0 
                END as roi
            FROM campanias c
            LEFT JOIN difusiones d ON d.idcampania = c.idcampania
            WHERE c.fecha_inicio >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(c.fecha_inicio, '%Y-%m')
            ORDER BY mes ASC
        ");

        return $resultado ? $resultado->getResultArray() : [];
    }

    public function getDistribucionEstados()
    {
        $resultado = $this->db->query("
            SELECT 
                estado,
                COUNT(*) as cantidad,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM campanias), 1) as porcentaje
            FROM campanias 
            GROUP BY estado
            ORDER BY cantidad DESC
        ");

        return $resultado ? $resultado->getResultArray() : [];
    }

    public function getROIPorMedio()
    {
        $resultado = $this->db->query("
            SELECT 
                m.nombre as medio,
                COUNT(DISTINCT d.idcampania) as campanas,
                COALESCE(SUM(d.presupuesto), 0) as inversion_total,
                COALESCE(SUM(d.leads_generados), 0) as leads_total,
                CASE 
                    WHEN SUM(d.presupuesto) > 0 
                    THEN ROUND(SUM(d.leads_generados) / SUM(d.presupuesto) * 100, 2)
                    ELSE 0 
                END as roi,
                CASE 
                    WHEN SUM(d.leads_generados) > 0 
                    THEN ROUND(SUM(d.presupuesto) / SUM(d.leads_generados), 2)
                    ELSE 0 
                END as costo_por_lead
            FROM medios m
            LEFT JOIN difusiones d ON d.idmedio = m.idmedio
            GROUP BY m.idmedio, m.nombre
            HAVING inversion_total > 0
            ORDER BY roi DESC
        ");

        return $resultado ? $resultado->getResultArray() : [];
    }

    public function getTopCampanas($limit = 5)
    {
        $resultado = $this->db->query("
            SELECT 
                c.idcampania,
                c.nombre,
                c.estado,
                COALESCE(SUM(d.presupuesto), 0) as inversion_total,
                COALESCE(SUM(d.leads_generados), 0) as leads_total,
                CASE 
                    WHEN SUM(d.presupuesto) > 0 
                    THEN ROUND(SUM(d.leads_generados) / SUM(d.presupuesto) * 100, 2)
                    ELSE 0 
                END as roi
            FROM campanias c
            LEFT JOIN difusiones d ON d.idcampania = c.idcampania
            GROUP BY c.idcampania
            HAVING inversion_total > 0
            ORDER BY roi DESC
            LIMIT {$limit}
        ");

        return $resultado ? $resultado->getResultArray() : [];
    }

    public function guardarDifusiones($idcampania, $medios)
    {
        $builder = $this->db->table('difusiones');

        // Eliminar registros previos
        $builder->where('idcampania', $idcampania)->delete();

        // Insertar nuevos
        foreach ($medios as $medio) {
            if (!isset($medio['idmedio']) || empty($medio['idmedio'])) {
                continue;
            }

            $builder->insert([
                'idcampania' => $idcampania,
                'idmedio' => $medio['idmedio'],
                'presupuesto' => floatval($medio['presupuesto'] ?? 0),
                'leads_generados' => intval($medio['leads_generados'] ?? 0)
            ]);
        }

        return true;
    }

    public function getMedios($idcampania)
    {
        $resultado = $this->db->table('difusiones d')
            ->select('
                d.*, 
                m.nombre, 
                m.descripcion as medio_descripcion,
                CASE 
                    WHEN d.presupuesto > 0 
                    THEN ROUND(d.leads_generados / d.presupuesto * 100, 2)
                    ELSE 0 
                END as roi,
                CASE 
                    WHEN d.leads_generados > 0 
                    THEN ROUND(d.presupuesto / d.leads_generados, 2)
                    ELSE 0 
                END as costo_por_lead
            ')
            ->join('medios m', 'm.idmedio = d.idmedio')
            ->where('d.idcampania', $idcampania)
            ->orderBy('d.presupuesto', 'DESC')
            ->get();

        return $resultado ? $resultado->getResultArray() : [];
    }

    public function eliminarCampana($idcampania)
    {
        // Verificar que la campaña existe
        $campana = $this->find($idcampania);
        if (!$campana) {
            throw new \InvalidArgumentException('La campaña no existe.');
        }
        
        // Verificar que no tenga leads asociados
        $leadsCount = $this->contarLeadsAsociados($idcampania);
        if ($leadsCount > 0) {
            throw new \InvalidArgumentException("No se puede eliminar la campaña porque tiene {$leadsCount} leads asociados.");
        }

        $this->db->transBegin();

        try {
            // Eliminar difusiones primero (por integridad referencial)
            $difusionesEliminadas = $this->db->table('difusiones')
                ->where('idcampania', $idcampania)
                ->delete();
            
            log_message('info', "Eliminadas {$difusionesEliminadas} difusiones de la campaña {$idcampania}");
            
            // Eliminar la campaña
            $result = $this->delete($idcampania);
            
            if (!$result) {
                throw new \Exception('Error al eliminar la campaña de la base de datos.');
            }

            $this->db->transCommit();
            log_message('info', "Campaña {$idcampania} eliminada exitosamente");
            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error al eliminar campaña {$idcampania}: " . $e->getMessage());
            throw $e;
        }
    }

    public function contarActivas()
    {
        return $this->where('estado', 'Activa')->countAllResults(false);
    }

    public function presupuestoTotal()
    {
        $result = $this->selectSum('presupuesto')->first();
        return $result['presupuesto'] ?? 0;
    }

    public function totalLeads()
    {
        $result = $this->db->table('difusiones')
            ->selectSum('leads_generados')
            ->get()
            ->getRow();
        
        return $result->leads_generados ?? 0;
    }

    public function getTotalLeads($idcampania)
    {
        $result = $this->db->table('difusiones')
            ->selectSum('leads_generados')
            ->where('idcampania', $idcampania)
            ->get()
            ->getRow();
        
        return $result->leads_generados ?? 0;
    }

    public function getEstados()
    {
        return [
            'Activa' => 'Activa',
            'Inactiva' => 'Inactiva'
        ];
    }

    public function getUsuarios()
    {
        $resultado = $this->db->table('usuarios u')
            ->select('u.idusuario, u.usuario, CONCAT(p.nombres, " ", p.apellidos) as nombre_completo')
            ->join('personas p', 'p.idpersona = u.idpersona', 'left')
            ->where('u.activo', true)
            ->orderBy('p.nombres', 'ASC')
            ->get();

        return $resultado ? $resultado->getResultArray() : [];
    }

    public function getMediosDisponibles()
    {
        $resultado = $this->db->table('medios')
            ->select('*')
            ->orderBy('nombre', 'ASC')
            ->get();

        return $resultado ? $resultado->getResultArray() : [];
    }

    public function puedeEditarse($idcampania)
    {
        $campana = $this->find($idcampania);
        return $campana !== null;
    }

    public function puedeEliminarse($idcampania)
    {
        $campana = $this->find($idcampania);
        return $campana !== null;
    }

    /**
     * Verifica si existe una campaña con el mismo nombre
     */
    public function existeNombre($nombre, $excluirId = null)
    {
        $builder = $this->where('nombre', $nombre);
        
        if ($excluirId) {
            $builder->where('idcampania !=', $excluirId);
        }
        
        return $builder->countAllResults() > 0;
    }
    
    /**
     * Cuenta los leads asociados a una campaña
     */
    public function contarLeadsAsociados($idcampania)
    {
        return $this->db->table('leads')
            ->where('idcampania', $idcampania)
            ->countAllResults();
    }

    public function crearCampana($datos)
    {
        // Validar datos requeridos
        if (empty($datos['nombre'])) {
            throw new \InvalidArgumentException('El nombre de la campaña es obligatorio.');
        }
        
        // Verificar duplicados
        if ($this->existeNombre($datos['nombre'])) {
            throw new \InvalidArgumentException('Ya existe una campaña con ese nombre.');
        }

        $campanaData = [
            'nombre' => trim($datos['nombre']),
            'descripcion' => !empty($datos['descripcion']) ? trim($datos['descripcion']) : null,
            'fecha_inicio' => $datos['fecha_inicio'] ?? null,
            'fecha_fin' => $datos['fecha_fin'] ?? null,
            'presupuesto' => floatval($datos['presupuesto'] ?? 0),
            'estado' => $datos['estado'] ?? 'Activa',
            'responsable' => !empty($datos['responsable']) ? intval($datos['responsable']) : null
        ];

        return $this->insert($campanaData);
    }


    public function actualizarCampana($idcampania, $datos)
    {
        $campanaData = [];

        if (isset($datos['nombre'])) {
            $campanaData['nombre'] = $datos['nombre'];
        }
        if (isset($datos['descripcion'])) {
            $campanaData['descripcion'] = $datos['descripcion'];
        }
        if (isset($datos['fecha_inicio'])) {
            $campanaData['fecha_inicio'] = $datos['fecha_inicio'];
        }
        if (isset($datos['fecha_fin'])) {
            $campanaData['fecha_fin'] = $datos['fecha_fin'];
        }
        if (isset($datos['presupuesto'])) {
            $campanaData['presupuesto'] = floatval($datos['presupuesto']);
        }
        if (isset($datos['estado'])) {
            $campanaData['estado'] = $datos['estado'];
        }
        if (isset($datos['responsable'])) {
            $campanaData['responsable'] = $datos['responsable'];
        }

        if (empty($campanaData)) {
            return false;
        }

        return $this->update($idcampania, $campanaData);
    }
}