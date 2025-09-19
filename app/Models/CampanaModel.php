<?php

namespace App\Models;
use CodeIgniter\Model;

class CampanaModel extends Model
{
    protected $table = 'campanias';
    protected $primaryKey = 'idcampania';
    
    protected $allowedFields = [
        'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'presupuesto', 
        'estado', 'prioridad', 'segmento', 'responsable', 'objetivos', 'notas',
        'categoria', 'tags', 'fecha_creacion', 'creado_por', 'fecha_finalizacion'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[100]',
        'presupuesto' => 'required|numeric|greater_than[0]',
        'fecha_inicio' => 'required|valid_date',
        'fecha_fin' => 'required|valid_date',
        'estado' => 'in_list[borrador,activa,pausada,finalizada,cancelada]',
        'prioridad' => 'in_list[alta,media,baja]'
    ];

    // ===== MÉTODOS PRINCIPALES =====

    /**
     * Obtener todas las campañas con detalles completos
     */
    public function getAllWithDetails()
    {
        return $this->select('
                campanias.*, 
                u.nombre as responsable_nombre,
                u.email as responsable_email,
                COALESCE(SUM(d.inversion), 0) as inversion_total,
                COALESCE(SUM(d.leads_generados), 0) as leads_total,
                CASE 
                    WHEN COALESCE(SUM(d.inversion), 0) > 0 
                    THEN ROUND(COALESCE(SUM(d.leads_generados), 0) / SUM(d.inversion) * 100, 2)
                    ELSE 0 
                END as roi
            ')
            ->join('usuarios u', 'u.idusuario = campanias.responsable', 'left')
            ->join('difusiones d', 'd.idcampania = campanias.idcampania', 'left')
            ->groupBy('campanias.idcampania')
            ->orderBy('campanias.fecha_creacion', 'DESC')
            ->findAll();
    }

    /**
     * Obtener campañas filtradas
     */
    public function getCampanasFiltered($filtros = [])
    {
        $builder = $this->select('
                campanias.*, 
                u.nombre as responsable_nombre,
                COALESCE(SUM(d.inversion), 0) as inversion_total,
                COALESCE(SUM(d.leads_generados), 0) as leads_total,
                CASE 
                    WHEN COALESCE(SUM(d.inversion), 0) > 0 
                    THEN ROUND(COALESCE(SUM(d.leads_generados), 0) / SUM(d.inversion) * 100, 2)
                    ELSE 0 
                END as roi
            ')
            ->join('usuarios u', 'u.idusuario = campanias.responsable', 'left')
            ->join('difusiones d', 'd.idcampania = campanias.idcampania', 'left');

        // Aplicar filtros
        if (!empty($filtros['search'])) {
            $builder->groupStart()
                ->like('campanias.nombre', $filtros['search'])
                ->orLike('campanias.descripcion', $filtros['search'])
                ->orLike('campanias.tags', $filtros['search'])
                ->groupEnd();
        }

        if (!empty($filtros['status'])) {
            $builder->where('campanias.estado', $filtros['status']);
        }

        if (!empty($filtros['priority'])) {
            $builder->where('campanias.prioridad', $filtros['priority']);
        }

        if (!empty($filtros['responsible'])) {
            $builder->where('campanias.responsable', $filtros['responsible']);
        }

        if (!empty($filtros['categoria'])) {
            $builder->where('campanias.categoria', $filtros['categoria']);
        }

        // Filtros de presupuesto
        if (!empty($filtros['budget_min'])) {
            $builder->where('campanias.presupuesto >=', $filtros['budget_min']);
        }

        if (!empty($filtros['budget_max'])) {
            $builder->where('campanias.presupuesto <=', $filtros['budget_max']);
        }

        // Filtros de fecha
        if (!empty($filtros['date_start'])) {
            $builder->where('campanias.fecha_inicio >=', $filtros['date_start']);
        }

        if (!empty($filtros['date_end'])) {
            $builder->where('campanias.fecha_fin <=', $filtros['date_end']);
        }

        return $builder->groupBy('campanias.idcampania')
            ->orderBy('campanias.fecha_creacion', 'DESC')
            ->findAll();
    }

    /**
     * Obtener detalle completo de una campaña
     */
    public function getDetalle($idcampania)
    {
        $campana = $this->select('
                campanias.*, 
                u.nombre as responsable_nombre,
                u.email as responsable_email,
                uc.nombre as creado_por_nombre
            ')
            ->join('usuarios u', 'u.idusuario = campanias.responsable', 'left')
            ->join('usuarios uc', 'uc.idusuario = campanias.creado_por', 'left')
            ->find($idcampania);

        if ($campana) {
            // Agregar métricas calculadas
            $metricas = $this->getMetricasCampana($idcampania);
            $campana = array_merge($campana, $metricas);
        }

        return $campana;
    }

    /**
     * Obtener métricas generales del sistema
     */
    public function getMetricas()
    {
        // Total de campañas
        $total_campanas = $this->countAllResults(false);
        
        // Campañas por estado
        $activas = $this->where('estado', 'activa')->countAllResults(false);
        $pausadas = $this->where('estado', 'pausada')->countAllResults(false);
        $finalizadas = $this->where('estado', 'finalizada')->countAllResults(false);
        $borradores = $this->where('estado', 'borrador')->countAllResults(false);

        // Presupuesto total y gastado
        $presupuesto_result = $this->selectSum('presupuesto')->first();
        $presupuesto_total = $presupuesto_result['presupuesto'] ?? 0;

        $gastado_result = $this->db->table('difusiones')
            ->selectSum('inversion')
            ->get()
            ->getRow();
        $presupuesto_gastado = $gastado_result->inversion ?? 0;

        // Total de leads
        $leads_result = $this->db->table('difusiones')
            ->selectSum('leads_generados')
            ->get()
            ->getRow();
        $total_leads = $leads_result->leads_generados ?? 0;

        // ROI promedio
        $roi_promedio = $presupuesto_gastado > 0 ? 
            round(($total_leads * 100) / $presupuesto_gastado, 2) : 0;

        // Crecimiento mensual
        $mes_anterior = date('Y-m-d', strtotime('-1 month'));
        $campanas_mes_anterior = $this->where('fecha_creacion <', $mes_anterior)
            ->countAllResults(false);
        
        $crecimiento = $campanas_mes_anterior > 0 ? 
            round((($total_campanas - $campanas_mes_anterior) / $campanas_mes_anterior) * 100, 1) : 0;

        return [
            'total_campanas' => $total_campanas,
            'activas' => $activas,
            'pausadas' => $pausadas,
            'finalizadas' => $finalizadas,
            'borradores' => $borradores,
            'presupuesto_total' => $presupuesto_total,
            'presupuesto_gastado' => $presupuesto_gastado,
            'presupuesto_disponible' => $presupuesto_total - $presupuesto_gastado,
            'porcentaje_gastado' => $presupuesto_total > 0 ? 
                round(($presupuesto_gastado / $presupuesto_total) * 100, 1) : 0,
            'total_leads' => $total_leads,
            'roi_promedio' => $roi_promedio,
            'crecimiento_mensual' => $crecimiento,
            'conversion_promedio' => $this->getConversionPromedio()
        ];
    }

    /**
     * Obtener métricas específicas de una campaña
     */
    public function getMetricasCampana($idcampania)
    {
        $result = $this->db->table('difusiones')
            ->select('
                COALESCE(SUM(inversion), 0) as inversion_total,
                COALESCE(SUM(leads_generados), 0) as leads_total,
                COUNT(*) as medios_count
            ')
            ->where('idcampania', $idcampania)
            ->get()
            ->getRow();

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

    /**
     * Obtener analytics para gráficos
     */
    public function getAnalytics()
    {
        return [
            'rendimiento_mensual' => $this->getRendimientoMensual(),
            'distribucion_estados' => $this->getDistribucionEstados(),
            'roi_por_medio' => $this->getROIPorMedio(),
            'tendencias' => $this->getTendenciasCampanas()
        ];
    }

    /**
     * Obtener rendimiento mensual para gráficos
     */
    public function getRendimientoMensual()
    {
        $resultado = $this->db->query("
            SELECT 
                DATE_FORMAT(c.fecha_creacion, '%Y-%m') as mes,
                COUNT(c.idcampania) as campanas,
                COALESCE(SUM(d.leads_generados), 0) as leads,
                COALESCE(SUM(d.inversion), 0) as inversion,
                CASE 
                    WHEN SUM(d.inversion) > 0 
                    THEN ROUND(SUM(d.leads_generados) / SUM(d.inversion) * 100, 2)
                    ELSE 0 
                END as roi
            FROM campanias c
            LEFT JOIN difusiones d ON d.idcampania = c.idcampania
            WHERE c.fecha_creacion >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(c.fecha_creacion, '%Y-%m')
            ORDER BY mes ASC
        ")->getResultArray();

        // Completar meses faltantes
        $meses_completos = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes = date('Y-m', strtotime("-{$i} months"));
            $encontrado = false;
            
            foreach ($resultado as $row) {
                if ($row['mes'] === $mes) {
                    $meses_completos[] = $row;
                    $encontrado = true;
                    break;
                }
            }
            
            if (!$encontrado) {
                $meses_completos[] = [
                    'mes' => $mes,
                    'campanas' => 0,
                    'leads' => 0,
                    'inversion' => 0,
                    'roi' => 0
                ];
            }
        }

        return $meses_completos;
    }

    /**
     * Obtener distribución de estados para gráfico dona
     */
    public function getDistribucionEstados()
    {
        return $this->db->query("
            SELECT 
                estado,
                COUNT(*) as cantidad,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM campanias), 1) as porcentaje
            FROM campanias 
            GROUP BY estado
            ORDER BY cantidad DESC
        ")->getResultArray();
    }

    /**
     * Obtener ROI por medio de difusión
     */
    public function getROIPorMedio()
    {
        return $this->db->query("
            SELECT 
                m.nombre as medio,
                COUNT(DISTINCT d.idcampania) as campanas,
                COALESCE(SUM(d.inversion), 0) as inversion_total,
                COALESCE(SUM(d.leads_generados), 0) as leads_total,
                CASE 
                    WHEN SUM(d.inversion) > 0 
                    THEN ROUND(SUM(d.leads_generados) / SUM(d.inversion) * 100, 2)
                    ELSE 0 
                END as roi,
                CASE 
                    WHEN SUM(d.leads_generados) > 0 
                    THEN ROUND(SUM(d.inversion) / SUM(d.leads_generados), 2)
                    ELSE 0 
                END as costo_por_lead
            FROM medios m
            LEFT JOIN difusiones d ON d.idmedio = m.idmedio
            GROUP BY m.idmedio, m.nombre
            HAVING inversion_total > 0
            ORDER BY roi DESC
        ")->getResultArray();
    }

    /**
     * Obtener top campañas por rendimiento
     */
    public function getTopCampanas($limit = 5)
    {
        return $this->db->query("
            SELECT 
                c.idcampania,
                c.nombre,
                c.estado,
                c.prioridad,
                COALESCE(SUM(d.inversion), 0) as inversion_total,
                COALESCE(SUM(d.leads_generados), 0) as leads_total,
                CASE 
                    WHEN SUM(d.inversion) > 0 
                    THEN ROUND(SUM(d.leads_generados) / SUM(d.inversion) * 100, 2)
                    ELSE 0 
                END as roi
            FROM campanias c
            LEFT JOIN difusiones d ON d.idcampania = c.idcampania
            GROUP BY c.idcampania
            HAVING inversion_total > 0
            ORDER BY roi DESC
            LIMIT {$limit}
        ")->getResultArray();
    }

    /**
     * Obtener rendimiento temporal según período
     */
    public function getRendimientoTemporal($periodo = '30d')
    {
        $days = [
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '12m' => 365
        ];

        $dias = $days[$periodo] ?? 30;
        $formato = $dias <= 30 ? '%Y-%m-%d' : '%Y-%m';

        return $this->db->query("
            SELECT 
                DATE_FORMAT(c.fecha_creacion, '{$formato}') as fecha,
                COUNT(c.idcampania) as campanas_creadas,
                COALESCE(SUM(d.leads_generados), 0) as leads,
                COALESCE(SUM(d.inversion), 0) as inversion
            FROM campanias c
            LEFT JOIN difusiones d ON d.idcampania = c.idcampania
            WHERE c.fecha_creacion >= DATE_SUB(NOW(), INTERVAL {$dias} DAY)
            GROUP BY DATE_FORMAT(c.fecha_creacion, '{$formato}')
            ORDER BY fecha ASC
        ")->getResultArray();
    }

    /**
     * Obtener tendencias de campañas
     */
    public function getTendenciasCampanas()
    {
        return $this->db->query("
            SELECT 
                'campanas_activas' as metrica,
                COUNT(*) as valor_actual,
                (SELECT COUNT(*) FROM campanias 
                 WHERE estado = 'activa' 
                 AND fecha_creacion <= DATE_SUB(NOW(), INTERVAL 1 MONTH)) as valor_anterior
            FROM campanias 
            WHERE estado = 'activa'
            
            UNION ALL
            
            SELECT 
                'roi_promedio' as metrica,
                ROUND(AVG(CASE 
                    WHEN d.inversion > 0 
                    THEN d.leads_generados / d.inversion * 100 
                    ELSE 0 
                END), 2) as valor_actual,
                (SELECT ROUND(AVG(CASE 
                    WHEN d2.inversion > 0 
                    THEN d2.leads_generados / d2.inversion * 100 
                    ELSE 0 
                END), 2)
                 FROM difusiones d2 
                 JOIN campanias c2 ON c2.idcampania = d2.idcampania
                 WHERE c2.fecha_creacion <= DATE_SUB(NOW(), INTERVAL 1 MONTH)) as valor_anterior
            FROM difusiones d
            JOIN campanias c ON c.idcampania = d.idcampania
            WHERE c.fecha_creacion > DATE_SUB(NOW(), INTERVAL 1 MONTH)
        ")->getResultArray();
    }

    // ===== MÉTODOS DE GESTIÓN =====

    /**
     * Guardar medios/difusiones de una campaña
     */
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
                'inversion' => floatval($medio['inversion'] ?? 0),
                'leads_generados' => intval($medio['leads_generados'] ?? 0),
                'objetivo_leads' => intval($medio['objetivo_leads'] ?? 0),
                'cpc' => floatval($medio['cpc'] ?? 0),
                'cpm' => floatval($medio['cpm'] ?? 0),
                'impresiones' => intval($medio['impresiones'] ?? 0),
                'clics' => intval($medio['clics'] ?? 0),
                'creado' => date('Y-m-d H:i:s'),
                'estado' => $medio['estado'] ?? 'activo'
            ]);
        }

        // Actualizar fecha de última modificación
        $this->update($idcampania, ['fecha_actualizacion' => date('Y-m-d H:i:s')]);
    }

    /**
     * Obtener medios de una campaña
     */
    public function getMedios($idcampania)
    {
        return $this->db->table('difusiones d')
            ->select('
                d.*, 
                m.nombre, 
                m.tipo,
                m.descripcion as medio_descripcion,
                CASE 
                    WHEN d.inversion > 0 
                    THEN ROUND(d.leads_generados / d.inversion * 100, 2)
                    ELSE 0 
                END as roi,
                CASE 
                    WHEN d.leads_generados > 0 
                    THEN ROUND(d.inversion / d.leads_generados, 2)
                    ELSE 0 
                END as costo_por_lead,
                CASE 
                    WHEN d.clics > 0 
                    THEN ROUND((d.leads_generados / d.clics) * 100, 2)
                    ELSE 0 
                END as conversion_rate
            ')
            ->join('medios m', 'm.idmedio = d.idmedio')
            ->where('d.idcampania', $idcampania)
            ->orderBy('d.inversion', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtener medios para duplicar campaña
     */
    public function getMediosParaDuplicar($idcampania)
    {
        return $this->db->table('difusiones d')
            ->select('d.idmedio, d.inversion, d.objetivo_leads, d.cpc, d.cpm')
            ->where('d.idcampania', $idcampania)
            ->get()
            ->getResultArray();
    }

    /**
     * Eliminar campaña y sus relaciones
     */
    public function eliminarCampana($idcampania)
    {
        $this->db->transBegin();

        try {
            // Eliminar difusiones
            $this->db->table('difusiones')->where('idcampania', $idcampania)->delete();
            
            // Eliminar actividad
            $this->db->table('campana_actividad')->where('idcampania', $idcampania)->delete();
            
            // Eliminar archivos adjuntos si existen
            $this->db->table('campana_archivos')->where('idcampania', $idcampania)->delete();
            
            // Eliminar la campaña
            $this->delete($idcampania);

            $this->db->transCommit();
            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Obtener actividad/historial de una campaña
     */
    public function getActividad($idcampania, $limit = 10)
    {
        return $this->db->table('campana_actividad ca')
            ->select('
                ca.*, 
                u.nombre as usuario_nombre,
                u.email as usuario_email
            ')
            ->join('usuarios u', 'u.idusuario = ca.usuario_id', 'left')
            ->where('ca.idcampania', $idcampania)
            ->orderBy('ca.fecha', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    // ===== MÉTODOS DE CONTEO Y CÁLCULO =====

    /**
     * Contar campañas activas
     */
    public function contarActivas()
    {
        return $this->where('estado', 'activa')->countAllResults(false);
    }

    /**
     * Obtener presupuesto total
     */
    public function presupuestoTotal()
    {
        $result = $this->selectSum('presupuesto')->first();
        return $result['presupuesto'] ?? 0;
    }

    /**
     * Obtener total de leads
     */
    public function totalLeads()
    {
        $result = $this->db->table('difusiones')
            ->selectSum('leads_generados')
            ->get()
            ->getRow();
        
        return $result->leads_generados ?? 0;
    }

    /**
     * Obtener total de leads de una campaña específica
     */
    public function getTotalLeads($idcampania)
    {
        $result = $this->db->table('difusiones')
            ->selectSum('leads_generados')
            ->where('idcampania', $idcampania)
            ->get()
            ->getRow();
        
        return $result->leads_generados ?? 0;
    }

    /**
     * Obtener conversiones de una campaña (placeholder)
     */
    public function getConversiones($idcampania)
    {
        // Este método dependería de si tienes una tabla de conversiones
        // Por ahora retornamos un cálculo estimado
        $leads = $this->getTotalLeads($idcampania);
        return round($leads * 0.15); // Asumiendo 15% de conversión
    }

    /**
     * Obtener conversión promedio del sistema
     */
    public function getConversionPromedio()
    {
        $totalLeads = $this->totalLeads();
        $totalConversiones = round($totalLeads * 0.15); // Placeholder
        
        return $totalLeads > 0 ? round(($totalConversiones / $totalLeads) * 100, 2) : 0;
    }

    // ===== MÉTODOS DE CONFIGURACIÓN =====

    /**
     * Obtener estados disponibles
     */
    public function getEstados()
    {
        return [
            'borrador' => 'Borrador',
            'activa' => 'Activa',
            'pausada' => 'Pausada',
            'finalizada' => 'Finalizada',
            'cancelada' => 'Cancelada'
        ];
    }

    /**
     * Obtener prioridades disponibles
     */
    public function getPrioridades()
    {
        return [
            'alta' => 'Alta',
            'media' => 'Media',
            'baja' => 'Baja'
        ];
    }

    /**
     * Obtener categorías de campaña
     */
    public function getCategorias()
    {
        return [
            'general' => 'General',
            'promocional' => 'Promocional',
            'lanzamiento' => 'Lanzamiento',
            'estacional' => 'Estacional',
            'retention' => 'Retención',
            'brand_awareness' => 'Brand Awareness'
        ];
    }

    // ===== MÉTODOS DE VALIDACIÓN =====

    /**
     * Validar si una campaña puede ser editada
     */
    public function puedeEditarse($idcampania)
    {
        $campana = $this->find($idcampania);
        return $campana && !in_array($campana['estado'], ['finalizada', 'cancelada']);
    }

    /**
     * Validar si una campaña puede ser eliminada
     */
    public function puedeEliminarse($idcampania)
    {
        $campana = $this->find($idcampania);
        return $campana && $campana['estado'] !== 'activa';
    }

    /**
     * Validar traslape de fechas con otras campañas del mismo responsable
     */
    public function validarTraslape($fechaInicio, $fechaFin, $responsable, $excluirId = null)
    {
        $builder = $this->where('responsable', $responsable)
            ->where('estado !=', 'cancelada')
            ->groupStart()
                ->where('fecha_inicio <=', $fechaFin)
                ->where('fecha_fin >=', $fechaInicio)
            ->groupEnd();

        if ($excluirId) {
            $builder->where('idcampania !=', $excluirId);
        }

        return $builder->countAllResults() > 0;
    }
}