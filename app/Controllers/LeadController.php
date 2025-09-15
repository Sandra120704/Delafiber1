<?php

namespace App\Controllers;

use App\Models\LeadModel;
use App\Models\PersonaModel;
use App\Models\CampanaModel;
use App\Models\ModalidadesModel;
use App\Models\Origen;
use App\Models\EtapaModel;
use App\Models\TareaModel;
use App\Models\SeguimientoModel;

class LeadController extends BaseController
{
    protected $leadModel;
    protected $personaModel;
    protected $campanaModel;
    protected $modalidadesModel;
    protected $origenModel;
    protected $etapaModel;
    protected $tareaModel;
    protected $seguimientoModel;

    public function __construct()
    {
        $this->leadModel = new LeadModel();
        $this->personaModel = new PersonaModel();
        $this->campanaModel = new CampanaModel();
        $this->modalidadesModel = new ModalidadesModel();
        $this->origenModel = new Origen();
        $this->etapaModel = new EtapaModel();
        $this->tareaModel = new TareaModel();
        $this->seguimientoModel = new SeguimientoModel();
    }

    public function index()
    {
        $data['leads'] = $this->leadModel->getLeadsConTodo();
        $data['etapas'] = $this->etapaModel->findAll();
        $data['campanias'] = $this->campanaModel->findAll();
        $data['modalidades'] = $this->modalidadesModel->findAll();
        $data['origenes'] = $this->origenModel->findAll();

        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        return view('leads/index', $data);
    }

    public function modalCrear($idpersona)
    {
        $persona = $this->personaModel->find($idpersona);
        if (!$persona) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Persona no encontrada'
            ]);
        }

        return view('leads/modals', [
            'persona' => $persona,
            'modalidades' => $this->modalidadesModel->findAll(),
            'origenes' => $this->origenModel->findAll(),
            'campanias' => $this->campanaModel->findAll(),
        ]);
    }

    public function guardar()
    {
        $post = $this->request->getPost();
        $idpersona = $post['idpersona'] ?? null;

        if (!$idpersona) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de persona no proporcionado.'
            ]);
        }

        if ($this->leadModel->where('idpersona', $idpersona)->first()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Esta persona ya está registrada como Lead.'
            ]);
        }

        $etapaInicial = $this->etapaModel->orderBy('orden', 'ASC')->first();
        if (!$etapaInicial) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay etapas configuradas en el sistema.'
            ]);
        }

        $dataLead = [
            'idpersona' => $idpersona,
            'idcampania' => $post['idcampania'] ?? null,
            'idmodalidad' => $post['idmodalidad'] ?? null,
            'idorigen' => $post['idorigen'] ?? null,
            'referido_por' => $post['referido_por'] ?? null,
            'estado' => 'Nuevo',
            'idetapa' => $etapaInicial['idetapa'],
            'idusuario' => session('idusuario') ?? 1,
            'idusuario_registro' => session('idusuario') ?? 1,
        ];

        try {
            $idlead = $this->leadModel->insert($dataLead);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lead registrado correctamente.',
                'idlead' => $idlead,
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al guardar el lead: ' . $e->getMessage()
            ]);
        }
    }

    public function detalle($idlead)
    {
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

        $tareas = $this->tareaModel->where('idlead', $idlead)->orderBy('fecha_inicio','ASC')->findAll();
        $seguimientos = $this->seguimientoModel->where('idlead', $idlead)->orderBy('fecha','ASC')->findAll();
        $modalidades = $this->modalidadesModel->findAll();

        return $this->response->setJSON([
            'success' => true,
            'lead' => $lead,
            'tareas' => $tareas,
            'seguimientos' => $seguimientos,
            'modalidades' => $modalidades
        ]);
    }

    public function actualizarEtapa()
    {
        $idlead = $this->request->getPost('idlead');
        $idetapa = $this->request->getPost('idetapa');

        if (!$idlead || !$idetapa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }

        $this->leadModel->actualizarEtapa($idlead, $idetapa);

        return $this->response->setJSON(['success' => true, 'message' => 'Etapa actualizada']);
    }

    public function eliminar()
    {
        $idlead = $this->request->getPost('idlead');
        if (!$idlead) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID de Lead no proporcionado']);
        }

        $this->leadModel->update($idlead, ['estado' => 'Descartado']);

        return $this->response->setJSON(['success' => true, 'message' => 'Lead descartado correctamente']);
    }
}
