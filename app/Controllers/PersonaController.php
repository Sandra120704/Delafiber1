<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DepartamentoModel;
use App\Models\DistritoModel;
use App\Models\LeadModel;
use App\Models\PersonaModel;

class PersonaController extends BaseController
{
    protected $personaModel;

    public function __construct()
    {
        $this->personaModel = new PersonaModel();
    }

    public function index()
    {
        $datos['personas'] = $this->personaModel->orderBy('idpersona','ACD')->findAll();
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('personas/index', $datos);
    }
    public function crear()
{
    $departamento = new DepartamentoModel();
    $distrito = new DistritoModel();

    $datos['departamentos'] = $departamento->findAll();
    $datos['distritos'] = $distrito->findAll();
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');

    return view('personas/crear', $datos);
}

public function editar($id)
{
    $persona = $this->personaModel->find($id);

    if (!$persona) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Persona no encontrada");
    }

    $departamento = new DepartamentoModel();
    $distrito = new DistritoModel();

    $datos['persona'] = $persona;
    $datos['departamentos'] = $departamento->findAll();
    $datos['distritos'] = $distrito->findAll();
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');

    return view('personas/crear', $datos); // reutiliza la misma vista
}

    public function guardar()
    {
        try {
            $data = [
                'dni' => $this->request->getPost('dni'),
                'apellidos' => $this->request->getPost('apellidos'),
                'nombres' => $this->request->getPost('nombres'),
                'correo' => $this->request->getPost('correo'),
                'telefono' => $this->request->getPost('telefono'),
                'direccion' => $this->request->getPost('direccion'),
                'iddistrito' => $this->request->getPost('iddistrito'),
            ];

            $id = $this->personaModel->insert($data);

            if ($id) {
                return $this->response->setJSON([
                    'success' => true,
                    'idpersona' => $id
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo registrar la persona',
                    'errors' => $this->personaModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ]);
        }
    }


    public function BuscadorDni($dni = "")
    {
        $api_endpoint = "https://api.decolecta.com/v1/reniec/dni?numero=" . $dni;
        $api_token = "sk_10191.lvruYS7kNuUFi4DEKPT7nBSTZcOFyuTa";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_token
        ]);
        $api_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($api_response === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => ''
            ]);
        }

        $decoded_response = json_decode($api_response, true);

        if ($http_code !== 200) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No encontramos a la persona'
            ]);
        }

        return $this->response->setJSON([
            'success'     => true,
            'apepaterno'  => $decoded_response['first_last_name'] ?? '',
            'apematerno'  => $decoded_response['second_last_name'] ?? '',
            'nombres'     => $decoded_response['first_name'] ?? ''
        ]);
    }
    public function eliminar($id)
    {
        $leadModel = new LeadModel();

        $tieneLeads = $leadModel->where('idpersona', $id)->countAllResults();

        if ($tieneLeads > 0) {
            return redirect()->to('personas')->with('error', 'No se puede eliminar la persona porque tiene leads asociados.');
        }

        $this->personaModel->delete($id);
        return redirect()->to('personas')->with('success', 'Persona eliminada correctamente.');
    }

}
