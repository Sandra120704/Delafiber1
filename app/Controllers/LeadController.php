<?php

namespace App\Controllers;

use App\Models\CampanaModel;
use App\Models\DifunsionModel;
use App\Models\LeadModel;
use App\Models\EtapaModel;
use App\Models\MedioModel;
use App\Models\ModalidadesModel;
use App\Models\Origen;
use App\Models\PersonaModel;
use App\Models\SeguimientoModel;
use App\Models\TareaModel;

class LeadController extends BaseController
{
    protected $leadModel;
    protected $personaModel;
    protected $campanaModel;
    protected $medioModel;
    protected $etapaModel;
    protected $tareaModel;
    protected $seguimientoModel;
    protected $modalidadesModel;
    protected $persona;

    public function __construct()
    {
        $this->leadModel       = new LeadModel();
        $this->personaModel    = new PersonaModel();
        $this->campanaModel    = new CampanaModel();
        $this->medioModel      = new MedioModel();
        $this->etapaModel      = new EtapaModel();
        $this->tareaModel      = new TareaModel();
        $this->seguimientoModel = new SeguimientoModel();
        $this->modalidadesModel = new ModalidadesModel();
        $this->persona = new PersonaModel();
    }

    public function index()
    {
        $data['etapas']    = $this->etapaModel->findAll();
        $data['campanias'] = $this->campanaModel->findAll();
        $data['medios']    = $this->medioModel->findAll();

        $builder = $this->leadModel->builder();
        $builder->select('
            leads.idlead,
            leads.idetapa,
            leads.estado,
            etapas.nombre as etapa,
            personas.nombres,
            personas.apellidos,
            personas.telefono,
            personas.correo,
            campanias.nombre as campania,
            medios.nombre as medio,
            usuarios.usuario
        ');
        $builder->join('personas', 'personas.idpersona = leads.idpersona');
        $builder->join('usuarios', 'usuarios.idusuario = leads.idusuario', 'left');
        $builder->join('difusiones d', 'd.iddifusion = leads.iddifusion', 'left');
        $builder->join('campanias', 'campanias.idcampania = d.idcampania', 'left');
        $builder->join('medios', 'medios.idmedio = d.idmedio', 'left');
        $builder->join('etapas', 'etapas.idetapa = leads.idetapa', 'left');
        $builder->where('leads.estado !=', 'Descartado');// SOLO activos

        $leads = $builder->get()->getResultArray();

        $leadsPorEtapa = [];

        $colorEstado = [
            'Nuevo' => '#007bff',
            'En proceso' => '#ffc107',
            'Convertido' => '#28a745',
            'Descartado' => '#6c757d'
        ];

        foreach ($leads as $lead) {
            $lead['estatus_color'] = $colorEstado[$lead['estado']] ?? '#007bff';
            $leadsPorEtapa[$lead['idetapa']][] = $lead;
        }
        $data['leadsPorEtapa'] =  $leadsPorEtapa;

        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        return view('leads/index', $data);
    }
public function modalCrear($idpersona)
{
    // Obtener persona como array u objeto (no Builder)
    $persona = $this->personaModel->find($idpersona); 
    if (!$persona) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Persona no encontrada'
        ]);
    }

    // Modalidades y orígenes
    $modalidades  = $this->modalidadesModel->findAll();
    $origenes     = (new Origen())->findAll();

    // Difusiones completas (método que devuelve array)
    $difusiones   = (new DifunsionModel())->getDifusionesCompletas();

    // Campañas y medios
    $campanias    = $this->campanaModel->findAll();
    $medios       = $this->medioModel->findAll();

    return view('leads/modals', [
        'persona'     => $persona,
        'modalidades' => $modalidades,
        'origenes'    => $origenes,
        'difusiones'  => $difusiones,
        'campanias'   => $campanias,
        'medios'      => $medios
    ]);
}

public function guardar()
{
    $idpersona = $this->request->getPost('idpersona');

    if (!$idpersona) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'ID de persona no proporcionado.'
        ]);
    }

    // Verificar si ya existe lead
    if ($this->leadModel->where('idpersona', $idpersona)->first()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Esta persona ya está registrada como Lead.'
        ]);
    }

    // Obtener la primera etapa del pipeline
    $etapaInicial = $this->etapaModel->orderBy('orden', 'ASC')->first();
    if (!$etapaInicial) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'No hay etapas configuradas en el sistema.'
        ]);
    }

    // Preparar datos a insertar
    $dataLead = [
        'idpersona'          => $idpersona,
        'iddifusion'         => $this->request->getPost('iddifusion') ?: null,
        'idmodalidad'        => $this->request->getPost('idmodalidad') ?: null,
        'idorigen'           => $this->request->getPost('idorigen') ?: null,
        'referido_por'       => $this->request->getPost('referido_por') ?: null,
        'estado'             => 'Nuevo',
        'idetapa'            => $etapaInicial['idetapa'],
        'idusuario'          => session('idusuario') ?? 1,
        'idusuario_registro' => session('idusuario') ?? 1
    ];

    try {
        $idlead = $this->leadModel->insert($dataLead);

        if ($idlead) {
            $persona = $this->personaModel->find($idpersona);
            $persona->idetapa = $dataLead['idetapa'];

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lead registrado correctamente.',
                'idlead'  => $idlead,
                'persona' => $persona
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se pudo registrar el lead.'
            ]);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al guardar el lead: ' . $e->getMessage()
        ]);
    }
}

    public function detalle($idlead)
    {
        // Traer lead con datos de la persona
        $lead = $this->leadModel
            ->select('leads.*, personas.nombres, personas.apellidos, personas.telefono, personas.correo, usuarios.usuario as usuario')
            ->join('personas', 'personas.idpersona = leads.idpersona', 'left')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario', 'left')
            ->where('leads.idlead', $idlead)
            ->first();

        if (!$lead) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lead no encontrado'
            ]);
        }

        // Tareas y seguimientos
        $tareas = $this->tareaModel->where('idlead', $idlead)->orderBy('fecha_programada','ASC')->findAll();
        $seguimientos = $this->seguimientoModel->where('idlead', $idlead)->orderBy('fecha','ASC')->findAll();

        // Modalidades
        $modalidades = $this->modalidadesModel->findAll();

        // Render parcial
        $html = view('leads/partials/detalles', [
            'lead' => $lead,
            'tareas' => $tareas,
            'seguimientos' => $seguimientos,
            'modalidades' => $modalidades
        ]);

        return $this->response->setJSON([
            'success' => true,
            'lead' => $lead,
            'tareas' => $tareas,
            'seguimientos' => $seguimientos,
            'modalidades' => $modalidades,
            'html' => $html
        ]);
    }

    public function actualizarEtapa()
    {
        $idlead = $this->request->getPost('idlead');
        $idetapa = $this->request->getPost('idetapa');

        if (!$idlead || !$idetapa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }

        $lead = $this->leadModel->find($idlead);
        if (!$lead) {
            return $this->response->setJSON(['success' => false, 'message' => 'Lead no encontrado']);
        }

        $this->leadModel->update($idlead, ['idetapa' => $idetapa]);
        return $this->response->setJSON(['success' => true, 'message' => 'Etapa actualizada']);
    }

    public function eliminar()
    {
        $idlead = $this->request->getPost('idlead');
        if (!$idlead) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID de Lead no proporcionado']);
        }

        $lead = $this->leadModel->find($idlead);
        if (!$lead) {
            return $this->response->setJSON(['success' => false, 'message' => 'Lead no encontrado']);
        }

        $this->leadModel->update($idlead, ['estado' => 'Descartado']);
        return $this->response->setJSON(['success' => true, 'message' => 'Lead desistido correctamente']);
    }

public function guardarTarea()
{
    $idlead = $this->request->getPost('idlead');
    $descripcion = $this->request->getPost('descripcion');

    if (!$idlead || !$descripcion) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Datos incompletos para la tarea'
        ]);
    }

    $id = $this->tareaModel->insert([
        'idlead'        => $idlead,
        'idusuario'     => session()->get('idusuario'),
        'descripcion'   => $descripcion,
        'fecha_registro'=> date('Y-m-d H:i:s')
    ]);

    if ($id) {
        return $this->response->setJSON([
            'success' => true,
            'tarea' => [
                'id'             => $id,
                'descripcion'    => $descripcion,
                'fecha_registro' => date('d/m/Y H:i')
            ]
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'message' => 'Error al guardar la tarea'
    ]);
}


public function guardarSeguimiento()
{
    $idlead = $this->request->getPost('idlead');
    $idmodalidad = $this->request->getPost('idmodalidad');
    $comentario = $this->request->getPost('comentario');

    if (!$idlead || !$idmodalidad || !$comentario) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Datos incompletos para el seguimiento'
        ]);
    }

    $id = $this->seguimientoModel->insert([
        'idlead'      => $idlead,
        'idusuario'   => session()->get('idusuario'),
        'idmodalidad' => $idmodalidad,
        'comentario'  => $comentario,
        'fecha'       => date('Y-m-d H:i:s')
    ]);

    if ($id) {
        return $this->response->setJSON([
            'success' => true,
            'seguimiento' => [
                'id'        => $id,
                'comentario'=> $comentario,
                'fecha'     => date('d/m/Y H:i')
            ]
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'message' => 'Error al guardar seguimiento'
    ]);
}

public function convertirALead($idpersona)
{
    // Verificar que la persona exista
    $persona = $this->personaModel->find($idpersona);
    if (!$persona) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Persona no encontrada.'
        ]);
    }

    // Verificar si ya existe un lead para esta persona
    $existeLead = $this->leadModel->where('idpersona', $idpersona)->first();
    if ($existeLead) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'El lead para esta persona ya existe.'
        ]);
    }

    // Insertar lead con datos básicos
    $dataLead = [
        'idpersona'         => $idpersona,
        'estado'            => 'Nuevo',
        'idetapa'           => 1, // etapa inicial
        'idusuario_registro'=> session('idusuario'),
        'idusuario'         => session('idusuario')
    ];

    $idlead = $this->leadModel->insert($dataLead);

    if ($idlead) {
        // Agregar la etapa al array de persona para que JS coloque la tarjeta en el Kanban
        $persona['idetapa'] = $dataLead['idetapa'];

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Lead creado correctamente.',
            'idlead'  => $idlead,
            'persona' => $persona
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'message' => 'Error al crear lead.'
    ]);
}

    public function validar($idpersona)
    {
        $existe = $this->leadModel->where('idpersona', $idpersona)->first() ? true : false;
        return $this->response->setJSON(['exists' => $existe]);
    }


}
