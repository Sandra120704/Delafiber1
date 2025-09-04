<?php
namespace App\Controllers;
use App\Models\LeadModel;
use App\Models\SeguimientoModel;
use App\Models\TareaModel;

class LeadController extends BaseController
{
    protected $leadModel;
    protected $seguimientoModel;
    protected $tareaModel;

    public function __construct()
    {
        $this->leadModel = new LeadModel();
        $this->seguimientoModel = new SeguimientoModel();
        $this->tareaModel = new TareaModel();
    }

    // Vista completa Kanban
    public function kanban()
    {
        $data = $this->getDatosLeads();
        // Carga completa: header + footer
        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        return view('leads/index', $data);
    }

    // Vista parcial para AJAX (solo contenido dinámico)
    public function listar()
    {
        $data = $this->getDatosLeads();
        return view('leads/partials/listado', $data); // crea una vista parcial leads/partials/listado.php
    }

    // Detalle lead (modal)
    public function detalle($id)
    {
        $lead = $this->leadModel->getLeadConPersona($id);
        $seguimientos = $this->seguimientoModel->getByLead($id);
        $tareas = $this->tareaModel->getByLead($id);

        return view('leads/partials/detalle', compact('lead','seguimientos','tareas'));
    }

    // Guardar lead
    public function guardar()
    {
        $data = $this->request->getPost();
        $data['idusuarioregistro'] = session()->get('idusuario');
        $data['estatus_global'] = 'nuevo';
        $data['fechasignacion'] = date('Y-m-d H:i:s');

        $idlead = $this->leadModel->insert($data);

        return $this->response->setJSON([
            'success' => $idlead ? true : false,
            'mensaje' => $idlead ? 'Lead guardado correctamente' : 'Error al guardar'
        ]);
    }

    // Avanzar etapa
    public function avanzarEtapa()
    {
        $idlead = $this->request->getPost('idlead');
        $nuevaEtapa = $this->request->getPost('idetapa');

        $success = $this->leadModel->update($idlead, ['idetapa' => $nuevaEtapa]);

        return $this->response->setJSON(['success' => $success ? true : false]);
    }

    // Guardar seguimiento
    public function guardarSeguimiento()
    {
        $data = $this->request->getPost();
        $data['fecha'] = date('Y-m-d H:i:s');
        $id = $this->seguimientoModel->insert($data);

        return $this->response->setJSON(['success' => $id ? true : false]);
    }

    // Guardar tarea
    public function guardarTarea()
    {
        $data = $this->request->getPost();
        $id = $this->tareaModel->insert($data);

        return $this->response->setJSON(['success' => $id ? true : false]);
    }

    // Método privado para obtener datos comunes
    private function getDatosLeads()
    {
        return [
            'pipelines' => $this->leadModel->getPipelines(),
            'etapas' => $this->leadModel->getEtapas(),
            'leads' => $this->leadModel->getAllLeads(),
            'usuarios' => $this->leadModel->getUsuarios(),
            'personas' => $this->leadModel->getPersonas(),
            'difusiones' => $this->leadModel->getDifusiones()
        ];
    }
}
