<?php

namespace App\Controllers;

use App\Models\CampanaModel;
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
        $builder->join('campanias', 'campanias.idcampania = leads.idcampania', 'left');
        $builder->join('medios', 'medios.idmedio = leads.idmedio', 'left');
        $builder->join('etapas', 'etapas.idetapa = leads.idetapa', 'left');
        $builder->where('leads.estado !=', 'desistido'); // SOLO activos

        $leads = $builder->get()->getResultArray();

        $leadsPorEtapa = [];
        foreach ($leads as $lead) {
            $lead['estatus_color'] = '#007bff'; // color por defecto
            $leadsPorEtapa[$lead['idetapa']][] = $lead;
        }
        $data['leadsPorEtapa'] = $leadsPorEtapa;

        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        return view('leads/index', $data);
    }

    public function modalCrear($idpersona)
    {
        $personaModel     = new PersonaModel();
        $campaniaModel    = new CampanaModel();
        $medioModel       = new MedioModel();
        $modalidadModel   = new ModalidadesModel();
        $origenModel      = new Origen();

        $persona      = $personaModel->find($idpersona);
        $campanas     = $campaniaModel->findAll();
        $medios       = $medioModel->findAll();
        $modalidades  = $modalidadModel->findAll();
        $origenes     = $origenModel->findAll();

        return view('leads/modals', [
            'persona'      => $persona,
            'campanas'     => $campanas,
            'medios'       => $medios,
            'modalidades'  => $modalidades,
            'origenes'     => $origenes
        ]);
    }

    public function guardar()
    {
        $data = [
            'idpersona'        => $this->request->getPost('idpersona'),
            'idorigen'         => $this->request->getPost('idorigen'),
            'idmedio'          => $this->request->getPost('idmedio'),
            'idcampania'       => $this->request->getPost('idcampania') ?: null,
            'referido_por'     => $this->request->getPost('referido_por') ?: null,
            'estado'           => 'nuevo',
            'idusuario_registro'=> session('idusuario'),
            'idusuario'        => session('idusuario'),
            'idetapa'          => 1 // etapa inicial
        ];

        $idlead = $this->leadModel->insert($data);
        $persona = $this->personaModel->find($data['idpersona']);

        return $this->response->setJSON([
            'success' => true,
            'idlead'  => $idlead,
            'idetapa' => $data['idetapa'],
            'persona' => $persona
        ]);
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

        $this->leadModel->update($idlead, ['estado' => 'desistido']);
        return $this->response->setJSON(['success' => true, 'message' => 'Lead desistido correctamente']);
    }

    public function guardarTarea()
    {
        $idlead = $this->request->getPost('idlead');
        $descripcion = $this->request->getPost('descripcion');

        if (!$idlead || !$descripcion) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }

        $id = $this->tareaModel->insert([
            'idlead'        => $idlead,
            'descripcion'   => $descripcion,
            'fecha'         => date('Y-m-d H:i:s') // CORRECCIÓN
        ]);

        if ($id) {
            return $this->response->setJSON([
                'success' => true,
                'tarea'   => ['descripcion' => $descripcion, 'fecha_registro' => date('d/m/Y H:i')]
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar tarea']);
        }
    }

    public function guardarSeguimiento()
    {
        $idlead = $this->request->getPost('idlead');
        $idmodalidad = $this->request->getPost('idmodalidad');
        $comentario = $this->request->getPost('comentario');

        if (!$idlead || !$idmodalidad || !$comentario) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }

        $id = $this->seguimientoModel->insert([
            'idlead'      => $idlead,
            'idusuario'   => session()->get('idusuario'),
            'idmodalidad' => $idmodalidad,
            'comentario'  => $comentario,
            'echa_programada'       => date('Y-m-d H:i:s') // CORRECCIÓN
        ]);

        if ($id) {
            return $this->response->setJSON([
                'success'     => true,
                'seguimiento' => [
                    'comentario' => $comentario,
                    'echa_programada'      => date('Y-m-d H:i:s')
                ]
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar seguimiento']);
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
            'estado'            => 'nuevo',
            'idetapa'           => 1, // etapa inicial
            'idusuario_registro'=> session('idusuario'),
            'idusuario'         => session('idusuario')
            // Puedes agregar más campos si quieres
        ];

        $idlead = $this->leadModel->insert($dataLead);

        if ($idlead) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lead creado correctamente.',
                'idlead'  => $idlead
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al crear lead.'
        ]);
    }

}
