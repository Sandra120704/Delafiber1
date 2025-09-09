<?php

namespace App\Controllers;

use App\Models\CampanaModel;
use App\Models\LeadModel;
use App\Models\EtapaModel;
use App\Models\MedioModel;
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

    public function __construct()
    {
        $this->leadModel    = new LeadModel();
        $this->personaModel = new PersonaModel();
        $this->campanaModel = new CampanaModel();
        $this->medioModel   = new MedioModel();
        $this->etapaModel   = new EtapaModel();
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

        $leads = $builder->get()->getResultArray();

        $leadsPorEtapa = [];
        foreach ($leads as $lead) {
            $leadsPorEtapa[$lead['idetapa']][] = $lead;
        }
        $data['leadsPorEtapa'] = $leadsPorEtapa;

        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        return view('leads/index', $data);
    }

    // Función única para abrir el modal
    public function modalCrear($idpersona)
    {
        $data['persona']  = $this->personaModel->find($idpersona) ?? [];
        $data['campanas'] = $this->campanaModel->findAll();
        $data['medios']   = $this->medioModel->findAll();
        $data['etapas']   = $this->etapaModel->findAll();

        return view('leads/modals', $data);
    }

    public function guardar()
    {
        $idpersona    = $this->request->getPost('idpersona');
        $idcampania   = $this->request->getPost('idcampana') ?? null;
        $idmedio      = $this->request->getPost('idmedio') ?? null;
        $referido_por = $this->request->getPost('referido_por');
        $origen       = $this->request->getPost('origen');

        $etapaInicial = $this->etapaModel->where('orden', 1)->first();
        $idetapa = $etapaInicial['idetapa'] ?? null;

        if(!$idetapa){
            return redirect()->back()->with('error', 'No se encontró etapa inicial.');
        }

        $data = [
            'idpersona'        => $idpersona,
            'idcampania'       => ($origen === 'campania') ? $idcampania : null,
            'idmedio'          => ($origen === 'referido') ? 3 : $idmedio,
            'idetapa'          => $idetapa,
            'referido_por'     => ($origen === 'referido') ? $referido_por : null,
            'fecha_registro'   => date('Y-m-d H:i:s'),
            'estado'           => 'nuevo',
            'idusuario'        => null,
            'idusuario_registro'=> session()->get('idusuario'),
        ];

        $this->leadModel->insert($data);

        return redirect()->to('personas')->with('success', 'Lead registrado correctamente.');
    }
    public function detalle($idlead)
    {
        $builder = $this->leadModel->builder();
        $builder->select('leads.*, personas.nombres, personas.apellidos, personas.telefono, personas.correo, campanias.nombre as campania, medios.nombre as medio');
        $builder->join('personas', 'personas.idpersona = leads.idpersona');
        $builder->join('campanias', 'campanias.idcampania = leads.idcampania', 'left');
        $builder->join('medios', 'medios.idmedio = leads.idmedio', 'left');
        $builder->where('leads.idlead', $idlead);

        $lead = $builder->get()->getRowArray(); // obtienes un array con todos los campos necesarios

        if (!$lead) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Lead no encontrado: $idlead");
        }

        $seguimientoModel = new SeguimientoModel();
        $tareaModel       = new TareaModel();

        $seguimientos = $seguimientoModel->where('idlead', $idlead)->findAll();
        $tareas       = $tareaModel->where('idlead', $idlead)->findAll();

        return view('leads/partials/detalles', [
            'lead'         => $lead,
            'seguimientos' => $seguimientos,
            'tareas'       => $tareas
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
    public function verificarDuplicado($idpersona)
    {
        $leadExistente = $this->leadModel->where('idpersona', $idpersona)
                                        ->where('estado !=', 'desistido') // opcional: solo leads activos
                                        ->first();

        return $this->response->setJSON([
            'exists' => $leadExistente ? true : false
        ]);
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

        // Actualizar estado a "desistido"
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

    $tareaModel = new TareaModel();
    $id = $tareaModel->insert([
        'idlead' => $idlead,
        'descripcion' => $descripcion,
        'fecha_registro' => date('Y-m-d H:i:s')
    ]);

    if ($id) {
        return $this->response->setJSON(['success' => true, 'message' => 'Tarea registrada', 'tarea' => ['descripcion' => $descripcion, 'fecha_registro' => date('Y-m-d H:i:s')] ]);
    } else {
        return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar tarea']);
    }
}





}
