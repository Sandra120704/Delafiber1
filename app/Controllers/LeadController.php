<?php

namespace App\Controllers;

use App\Models\CampanaModel;
use App\Models\LeadModel;
use App\Models\EtapaModel;
use App\Models\MedioModel;
use App\Models\PersonaModel;

class LeadController extends BaseController
{
    public function index()
    {
        $leadModel = new LeadModel();
        $etapaModel = new EtapaModel();

        $data['etapas'] = $etapaModel->findAll();

        $builder = $leadModel->builder();
        $builder->select('
            leads.idlead,
            leads.idetapa,
            personas.nombres,
            personas.apellidos,
            personas.telefono,
            personas.correo,
            campanias.nombre as campana,
            medios.nombre as medio,
            usuarios.usuario
        ');
        $builder->join('personas', 'personas.idpersona = leads.idpersona');
        $builder->join('usuarios', 'usuarios.idusuario = leads.idusuario');
        $builder->join('campanias', 'campanias.idcampania = leads.idcampania', 'left');
        $builder->join('medios', 'medios.idmedio = leads.idmedio', 'left');

        $leads = $builder->get()->getResultArray();

        // Agrupar leads por etapa
        $leadsPorEtapa = [];
        foreach ($leads as $lead) {
            $leadsPorEtapa[$lead['idetapa']][] = $lead;
        }

        $data['leadsPorEtapa'] = $leadsPorEtapa;

        // Cargar header y footer en variables para pasarlas a la vista
        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        // Cargar solo la vista principal, que imprimirá header y footer dentro
        return view('leads/index', $data);
    }
    public function crear($idpersona)
    {
        $personaModel = new PersonaModel();
        $etapaModel   = new EtapaModel();
        $campaniaModel = new CampanaModel();
        $medioModel    = new MedioModel();

        $data['persona']  = $personaModel->find($idpersona); // Datos de la persona
        $data['etapas']   = $etapaModel->findAll();
        $data['campanas'] = $campaniaModel->findAll();
        $data['medios']   = $medioModel->findAll();

        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        return view('leads/crear', $data);
    }

    public function guardar()
    {
        $session = session();
        $usuario_id = $session->get('idusuario');

        $leadModel = new LeadModel();
        $leadModel->insert([
            'idpersona' => $this->request->getPost('idpersona'),
            'idetapa'   => $this->request->getPost('idetapa'),
            'idusuario' => $usuario_id,
            'estado'    => 'Nuevo',
            'idcampania'=> $this->request->getPost('idcampania'),
            'idmedio'   => $this->request->getPost('idmedio'),
        ]);

        return redirect()->to('leads');
    }


}
