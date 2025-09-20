<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $table = 'leads'; 

    public function getTotalLeads(): int
    {
        return $this->db->table('leads')->countAllResults();
    }

    // Obtiene leads convertidos este mes
    public function getLeadsConvertidosEsteMes(): int
    {
        return $this->db->table('leads')
            ->where('estado', 'Convertido')
            ->where('MONTH(fecha_registro)', date('m'))
            ->where('YEAR(fecha_registro)', date('Y'))
            ->countAllResults();
    }

    // Obtiene campañas activas
    public function getCampaniasActivas(): int
    {
        return $this->db->table('campanias')
            ->where('estado', 'Activa')
            ->countAllResults();
    }

    // Obtiene tareas pendientes
    public function getTareasPendientes(): int
    {
        return $this->db->table('tareas')
            ->where('estado', 'Pendiente')
            ->countAllResults();
    }

    // Datos del pipeline para gráfico
    public function getPipelineData(): array
    {
        $builder = $this->db->table('etapas e');
        $builder->select('e.nombre, COUNT(l.idlead) as total');
        $builder->join('leads l', 'e.idetapa = l.idetapa', 'left');
        $builder->groupBy('e.idetapa, e.nombre');
        $builder->orderBy('e.orden');
        
        return $builder->get()->getResultArray();
    }

    // Datos de campañas para gráfico
    public function getCampanasData(): array
    {
        $builder = $this->db->table('campanias c');
        $builder->select('c.nombre, COUNT(l.idlead) as total_leads');
        $builder->join('leads l', 'c.idcampania = l.idcampania', 'left');
        $builder->where('c.estado', 'Activa');
        $builder->groupBy('c.idcampania, c.nombre');
        $builder->having('total_leads >', 0);
        
        return $builder->get()->getResultArray();
    }

    // Actividad reciente (últimos 10 leads)
    public function getActividadReciente(int $limit = 10): array
    {
        $builder = $this->db->table('leads l');
        $builder->select('p.nombres, p.apellidos, e.nombre as etapa_nombre, l.fecha_registro');
        $builder->join('personas p', 'l.idpersona = p.idpersona');
        $builder->join('etapas e', 'l.idetapa = e.idetapa');
        $builder->orderBy('l.fecha_registro', 'DESC');
        $builder->limit($limit);
        
        return $builder->get()->getResultArray();
    }

    // Rendimiento por usuario
    public function getRendimientoUsuarios(): array
    {
        $builder = $this->db->table('usuarios u');
        $builder->select('
            p.nombres, 
            p.apellidos, 
            COUNT(l.idlead) as total_leads,
            SUM(CASE WHEN l.estado = "Convertido" THEN 1 ELSE 0 END) as leads_convertidos
        ');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('leads l', 'u.idusuario = l.idusuario', 'left');
        $builder->where('u.idrol !=', 1); // Excluir admins
        $builder->groupBy('u.idusuario, p.nombres, p.apellidos');
        $builder->having('total_leads >', 0);
        $builder->orderBy('total_leads', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    // Métricas adicionales para análisis
    public function getConversionRate(): float
    {
        $total = $this->getTotalLeads();
        if ($total == 0) return 0.0;
        
        $convertidos = $this->db->table('leads')
            ->where('estado', 'Convertido')
            ->countAllResults();
            
        return round(($convertidos / $total) * 100, 2);
    }

    // Leads por mes (últimos 6 meses)
    public function getLeadsPorMes(int $meses = 6): array
    {
        $builder = $this->db->table('leads');
        $builder->select('DATE_FORMAT(fecha_registro, "%Y-%m") as mes, COUNT(*) as total');
        $builder->where('fecha_registro >=', date('Y-m-01', strtotime("-{$meses} months")));
        $builder->groupBy('mes');
        $builder->orderBy('mes');
        
        return $builder->get()->getResultArray();
    }

    // Top campaña del mes
    public function getTopCampanaDelMes(): ?array
    {
        $builder = $this->db->table('campanias c');
        $builder->select('c.nombre, COUNT(l.idlead) as total_leads');
        $builder->join('leads l', 'c.idcampania = l.idcampania');
        $builder->where('MONTH(l.fecha_registro)', date('m'));
        $builder->where('YEAR(l.fecha_registro)', date('Y'));
        $builder->groupBy('c.idcampania, c.nombre');
        $builder->orderBy('total_leads', 'DESC');
        $builder->limit(1);
        
        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    // Obtener leads por etapa para el día actual
    public function getLeadsHoy(): array
    {
        $builder = $this->db->table('leads l');
        $builder->select('p.nombres, p.apellidos, e.nombre as etapa_nombre');
        $builder->join('personas p', 'l.idpersona = p.idpersona');
        $builder->join('etapas e', 'l.idetapa = e.idetapa');
        $builder->where('DATE(l.fecha_registro)', date('Y-m-d'));
        $builder->orderBy('l.fecha_registro', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    // Estadísticas de leads por origen
    public function getLeadsPorOrigen(): array
    {
        $builder = $this->db->table('leads l');
        $builder->select('o.nombre, COUNT(l.idlead) as total');
        $builder->join('origenes o', 'l.idorigen = o.idorigen');
        $builder->groupBy('o.idorigen, o.nombre');
        $builder->orderBy('total', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}
?>