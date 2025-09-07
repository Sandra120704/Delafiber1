<?php namespace App\Controllers;

use App\Models\CampanaModel;
use CodeIgniter\Controller;

class CampanaController extends BaseController
{
    protected $campanaModel;

    public function __construct()
    {
        $this->campanaModel = new CampanaModel();
    }

    // Vista principal de campañas
    public function index()
    {
        $campanas = $this->campanaModel->getCampanasConLeads();
        $campanas_activas = $this->campanaModel->getCampanasActivas();
        $presupuesto_total = $this->campanaModel->getPresupuestoTotal();
        $total_leads = array_sum(array_column($campanas, 'total_leads'));

        return view('campanas/index', compact(
            'campanas', 'campanas_activas', 'presupuesto_total', 'total_leads'
        ));
    }

    // Formulario para crear o editar
    public function form($id = null)
    {
        $data = [];
        if ($id) {
            $data['campana'] = $this->campanaModel->find($id);
        }
        return view('campanas/form', $data);
    }

    // Guardar campaña
    public function guardar()
    {
        $datos = $this->request->getPost();

        if (!empty($datos['idcampania'])) {
            $this->campanaModel->update($datos['idcampania'], $datos);
            return redirect()->to(site_url('campanas'))->with('success', 'Campaña actualizada');
        } else {
            $this->campanaModel->insert($datos);
            return redirect()->to(site_url('campanas'))->with('success', 'Campaña creada');
        }
    }

    // Eliminar campaña
    public function eliminar($id)
    {
        $this->campanaModel->delete($id);
        return redirect()->to(site_url('campanas'))->with('success', 'Campaña eliminada');
    }

    // Cambiar estado (Activo/Inactivo)
    public function cambiarEstado($id)
    {
        $campana = $this->campanaModel->find($id);
        if ($campana) {
            $nuevoEstado = $campana['estado'] === 'Activo' ? 'Inactivo' : 'Activo';
            $this->campanaModel->update($id, ['estado' => $nuevoEstado]);
        }
        return redirect()->to(site_url('campanas'));
    }
}
