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
        $etapas = $this->leadModel->getEtapas(); // 👈 obtiene etapas
        $leads = $this->leadModel->getAllLeads(); // 👈 obtiene leads con joins

        return view('Leads/index', [
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            'leads'  => $leads,
            'etapas' => $etapas // 👈 ya disponible en la vista
        ]);
    }
    // Vista parcial para AJAX (solo contenido dinámico)
    public function listar()
    {
        $data = $this->getDatosLeads();
        return view('leads/partials/listado', $data); 
    }
    public function crear()
    {
        $idpersona = $this->request->getGet('idpersona');
        $persona = $this->leadModel->getPersona($idpersona); 
        $usuarios = $this->leadModel->getUsuarios();
        $etapas = $this->leadModel->getEtapas();
        $difusiones = $this->leadModel->getDifusiones();

        return view('leads/crear', compact('persona','usuarios','etapas','difusiones'));
    }



    // Detalle lead (modal)
    public function detalle($id)
    {
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'error' => 'ID no definido']);
        }

        $lead = $this->leadModel->getLeadConPersona($id);
        if (!$lead) {
            return $this->response->setJSON(['success' => false, 'error' => 'Lead no encontrado']);
        }

        $seguimientos = $this->seguimientoModel->getByLead($id) ?: [];
        $tareas = $this->tareaModel->getByLead($id) ?: [];

        // Retorna la vista parcial del modal
        return view('leads/partials/detalles', compact('lead', 'seguimientos', 'tareas'));
    }

    // Guardar lead
    public function guardar()
    {
        // ====== PRUEBA: forzar usuario en sesión ======
        if (!session()->get('idusuario')) {
            session()->set('idusuario', 1); // ID de un usuario válido de tu BD
        }
        /* Se incertadorn datos de pruebas, luego se procedera con la logacio. */

        $idusuario = session()->get('idusuario');
        if (!$idusuario) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No hay usuario en sesión. Por favor, inicia sesión.'
            ]);
        }

        $data = $this->request->getPost();
        $data['idusuarioregistro'] = $idusuario; 
        $data['estatus_global'] = 'nuevo';
        $data['fechasignacion'] = date('Y-m-d H:i:s');

        try {
            $this->leadModel->insert($data);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
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
    public function eliminar()
    {
        $idlead = $this->request->getPost('idlead');
        $success = $this->leadModel->delete($idlead);

        return $this->response->setJSON([
            'success' => $success ? true : false
        ]);
    }

}
