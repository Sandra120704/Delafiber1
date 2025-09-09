<?php

namespace App\Controllers;

use App\Models\CampanaModel;
use App\Models\LeadModel;
use App\Models\EtapaModel;
use App\Models\MedioModel;
use App\Models\PersonaModel;

class LeadController extends BaseController
{
    protected $leadModel;
    protected $personaModel;

    public function __construct()
    {
        $this->leadModel = new LeadModel();
        $this->personaModel = new PersonaModel();
    }

    public function index()
    {
        $etapaModel    = new EtapaModel();
        $campaniaModel = new CampanaModel();
        $medioModel    = new MedioModel();

        // Datos para selects
        $data['etapas']    = $etapaModel->findAll();
        $data['campanias'] = $campaniaModel->findAll();
        $data['medios']    = $medioModel->findAll();

        // Leads con joins
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

        // Agrupar por etapa
        $leadsPorEtapa = [];
        foreach ($leads as $lead) {
            $leadsPorEtapa[$lead['idetapa']][] = $lead;
        }
        $data['leadsPorEtapa'] = $leadsPorEtapa;

        // Header/footer
        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        return view('leads/index', $data);
    }

    public function crear($idpersona)
    {
        $personaModel = new PersonaModel();
        $etapaModel   = new EtapaModel();
        $campaniaModel = new CampanaModel();
        $medioModel    = new MedioModel();

        $data['persona']  = $personaModel->find($idpersona);
        $data['etapas']   = $etapaModel->findAll();
        $data['campanas'] = $campaniaModel->findAll();
        $data['medios']   = $medioModel->findAll();

        return view('leads/modals', $data); // 👈 nueva vista SOLO con el form
    }


   public function guardar()
{
    $idpersona  = $this->request->getPost('idpersona');
    $idcampania = $this->request->getPost('idcampania');
    $idmedio    = $this->request->getPost('idmedio');

    // Obtener la etapa inicial del pipeline principal
    $etapaModel = new \App\Models\EtapaModel();
    $etapaInicial = $etapaModel->where('orden', 1)->first(); // primera etapa del pipeline
    $idetapa = $etapaInicial['idetapa'] ?? null;

    if(!$idetapa){
        return redirect()->back()->with('error', 'No se encontró etapa inicial.');
    }

    $data = [
        'idpersona'  => $idpersona,
        'idcampania' => $idcampania,
        'idmedio'    => $idmedio,
        'idetapa'    => $idetapa,
        'fecha_registro' => date('Y-m-d H:i:s'),
        'estado'     => 'nuevo',
        'idusuario'  => session()->get('idusuario') ?? null,
    ];

    $this->leadModel->insert($data);

    return redirect()->to('personas')->with('success', 'Lead registrado correctamente.');
}

// LeadController.php
    public function modalCrear($idpersona)
    {
        $personaModel = new PersonaModel();
        $campanaModel = new CampanaModel();
        $medioModel   = new MedioModel();
        $etapaModel   = new EtapaModel();

        $data['persona'] = $personaModel->find($idpersona);
        $data['campanas'] = $campanaModel->findAll();
        $data['medios'] = $medioModel->findAll();
        $data['etapas'] = $etapaModel->findAll();

        return view('leads/modals', $data); // solo el contenido del modal
    }


}
