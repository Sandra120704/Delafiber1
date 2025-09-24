<?php

namespace App\Models;

use CodeIgniter\Model;

class TareaModel extends Model
{
    protected $table = 'tareas';
    protected $primaryKey = 'idtarea';
    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'idlead',
        'idusuario',
        'titulo',
        'descripcion',
        'tipo_tarea',
        'prioridad',
        'fecha_inicio',
        'fecha_fin',
        'fecha_vencimiento',
        'fecha_completado',
        'estado',
        'notas_resultado'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'idlead' => 'required|integer',
        'idusuario' => 'required|integer',
        'titulo' => 'required|min_length[5]|max_length[200]',
        'descripcion' => 'permit_empty|max_length[1000]',
        'tipo_tarea' => 'permit_empty|in_list[llamada,whatsapp,email,visita,reunion,seguimiento,documentacion]',
        'prioridad' => 'permit_empty|in_list[baja,media,alta,urgente]',
        'fecha_inicio' => 'permit_empty|valid_date',
        'fecha_fin' => 'permit_empty|valid_date',
        'estado' => 'permit_empty|in_list[Pendiente,En progreso,Completada]'
    ];

    protected $validationMessages = [
        'idlead' => [
            'required' => 'Debe seleccionar un lead.',
            'integer' => 'El lead debe ser válido.'
        ],
        'idusuario' => [
            'required' => 'Debe asignar la tarea a un usuario.',
            'integer' => 'El usuario debe ser válido.'
        ],
        'descripcion' => [
            'required' => 'La descripción es obligatoria.',
            'min_length' => 'La descripción debe tener al menos 10 caracteres.',
            'max_length' => 'La descripción no puede superar los 500 caracteres.'
        ],
        'fecha_inicio' => [
            'required' => 'La fecha de inicio es obligatoria.',
            'valid_date' => 'Debe proporcionar una fecha válida.'
        ],
        'fecha_fin' => [
            'required' => 'La fecha de finalización es obligatoria.',
            'valid_date' => 'Debe proporcionar una fecha válida.'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio.',
            'in_list' => 'El estado debe ser: Pendiente, En progreso o Completada.'
        ]
    ];

    protected $skipValidation = false;

    // Obtener tareas con información del lead y usuario
public function obtenerTareasConDetalles($filtros = [])
{
    $builder = $this->builder();
    $builder->select('
        t.*,
        p.nombres as lead_nombres,
        p.apellidos as lead_apellidos,
        p.telefono,
        p.correo,
        u.usuario as asignado_a,
        up.nombres as usuario_nombres,
        up.apellidos as usuario_apellidos
    ');
    $builder->from('tareas t');
    $builder->join('leads l', 't.idlead = l.idlead');
    $builder->join('personas p', 'l.idpersona = p.idpersona');
    $builder->join('usuarios u', 't.idusuario = u.idusuario');
    $builder->join('personas up', 'u.idpersona = up.idpersona');

    // Aplicar filtros
    if (isset($filtros['estado'])) {
        $builder->where('t.estado', $filtros['estado']);
    }

    if (isset($filtros['usuario'])) {
        $builder->where('t.idusuario', $filtros['usuario']);
    }

    if (isset($filtros['fecha_desde'])) {
        $builder->where('t.fecha_inicio >=', $filtros['fecha_desde']);
    }

    if (isset($filtros['fecha_hasta'])) {
        $builder->where('t.fecha_fin <=', $filtros['fecha_hasta']);
    }

    $builder->orderBy('t.fecha_inicio', 'ASC');

    return $builder->get()->getResultArray();
}
    // Obtener tareas del día actual
    public function obtenerTareasHoy($usuarioId = null)
{
    $builder = $this->db->table('tareas t');
    $builder->select('
        t.*,
        p.nombres as lead_nombres,
        p.apellidos as lead_apellidos,
        p.telefono
    ');
    $builder->join('leads l', 't.idlead = l.idlead');
    $builder->where('DATE(t.fecha_inicio)', date('Y-m-d'));

    if ($usuarioId) {
        $builder->where('t.idusuario', $usuarioId);
    }

    $builder->orderBy('t.fecha_inicio', 'ASC');

    return $builder->get()->getResultArray();
}

    // Obtener tareas pendientes por usuario
    public function obtenerTareasPendientes($usuarioId)
    {
        $builder = $this->db->table('tareas t');
        $builder->select('
            t.*,
            p.nombres as lead_nombres,
            p.apellidos as lead_apellidos
        ');
        $builder->join('leads l', 't.idlead = l.idlead');
        $builder->join('personas p', 'l.idpersona = p.idpersona');
        $builder->where('t.idusuario', $usuarioId);
        $builder->where('t.estado', 'Pendiente');
        $builder->orderBy('t.fecha_inicio', 'ASC');

        return $builder->get()->getResultArray();
    }

    // Estadísticas de tareas
    public function obtenerEstadisticas($usuarioId = null)
    {
        $estadisticas = [
            'total' => 0,
            'pendientes' => 0,
            'proceso' => 0,
            'completadas' => 0,
            'vencidas' => 0
        ];

        $builder = $this->db->table('tareas t');

        if ($usuarioId) {
            $builder->where('idusuario', $usuarioId);
        }

        $estadisticas['total'] = $builder->countAllResults(false);

        // Pendientes
        $estadisticas['pendientes'] = $this->db->table('tareas')
            ->where($usuarioId ? ['idusuario' => $usuarioId, 'estado' => 'Pendiente'] : ['estado' => 'Pendiente'])
            ->countAllResults();

        // En proceso
        $estadisticas['proceso'] = $this->db->table('tareas')
            ->where($usuarioId ? ['idusuario' => $usuarioId, 'estado' => 'En progreso'] : ['estado' => 'En progreso'])
            ->countAllResults();

        // Completadas
        $estadisticas['completadas'] = $this->db->table('tareas')
            ->where($usuarioId ? ['idusuario' => $usuarioId, 'estado' => 'Completada'] : ['estado' => 'Completada'])
            ->countAllResults();

        // Vencidas (pendientes con fecha de fin menor a hoy)
        $builder = $this->db->table('tareas t');
        $builder->where('estado !=', 'Completada');
        $builder->where('fecha_fin <', date('Y-m-d'));
        if ($usuarioId) {
            $builder->where('idusuario', $usuarioId);
        }
        $estadisticas['vencidas'] = $builder->countAllResults();

        return $estadisticas;
    }

    // Obtener tareas para calendario
    public function obtenerTareasParaCalendario($mes, $anio)
    {
        $builder = $this->db->table('tareas t');
        $builder->select('
            t.*,
            p.nombres as lead_nombres,
            p.apellidos as lead_apellidos
        ');
        $builder->join('leads l', 't.idlead = l.idlead');
        $builder->join('personas p', 'l.idpersona = p.idpersona');
        $builder->where('MONTH(t.fecha_inicio)', $mes);
        $builder->where('YEAR(t.fecha_inicio)', $anio);

        return $builder->get()->getResultArray();
    }

    // Búsqueda de tareas
    public function buscarTareas($termino)
    {
        $builder = $this->db->table('tareas t');
        $builder->select('
            t.*,
            p.nombres as lead_nombres,
            p.apellidos as lead_apellidos
        ');
        $builder->join('leads l', 't.idlead = l.idlead');
        $builder->join('personas p', 'l.idpersona = p.idpersona');
        
        $builder->groupStart();
        $builder->like('t.descripcion', $termino);
        $builder->orLike('p.nombres', $termino);
        $builder->orLike('p.apellidos', $termino);
        $builder->groupEnd();

        $builder->orderBy('t.fecha_inicio', 'DESC');

        return $builder->get()->getResultArray();
    }
    // Agregar al TareaModel
    public function obtenerPorLead($idlead)
    {
        return $this->where('idlead', $idlead)->findAll();
    }

    public function contarTareasHoy()
    {
        return $this->where('DATE(fecha_inicio)', date('Y-m-d'))
                    ->where('estado !=', 'completada')
                    ->countAllResults();
    }

    public function contarTareasVencidas()
    {
        return $this->where('fecha_fin <', date('Y-m-d'))
                    ->where('estado !=', 'completada')
                    ->countAllResults();
    }
}
