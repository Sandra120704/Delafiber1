<?php

namespace App\Controllers;

use App\Models\CampanaModel;
use App\Models\CampaniaModel;
use App\Models\DepartamentoModel;
use App\Models\DifunsionModel;
use App\Models\DistritoModel;
use App\Models\LeadModel;
use App\Models\MedioModel;
use App\Models\ModalidadesModel;
use App\Models\Origen;
use App\Models\OrigenModel;
use App\Models\PersonaModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

/**
 * @property \CodeIgniter\HTTP\IncomingRequest $request
 */
class PersonaController extends BaseController
{
    use ResponseTrait;

    protected $personaModel;
    protected $departamentoModel;
    protected $distritoModel;
    protected $leadModel;
    protected $campaniaModel;
    protected $modalidadesModel;
    protected $origenModel;

    public function __construct()
    {
        $this->personaModel = new PersonaModel();
        $this->departamentoModel = new DepartamentoModel();
        $this->distritoModel = new DistritoModel();
        $this->leadModel = new LeadModel();
        $this->campaniaModel = new CampanaModel();
        $this->modalidadesModel = new ModalidadesModel();
        $this->origenModel = new Origen();
    }

    public function index()
    {
        $perPage = 10;
        $q = $this->request->getGet('q');

        $model = $this->personaModel;

        if ($q) {
            $model = $model->groupStart()
                ->like('nombres', $q)
                ->orLike('dni', $q)
                ->orLike('telefono', $q)
                ->orLike('correo', $q)
                ->groupEnd();
        }

        $data = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'personas' => $model->orderBy('idpersona', 'DESC')->paginate($perPage),
            'pager' => $model->pager,
            'q' => $q,
        ];

        return view('personas/index', $data);
    }

    public function crear()
    {
        $data = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'departamentos' => $this->departamentoModel->findAll(),
            'distritos' => $this->distritoModel->findAll(),
        ];

        return view('personas/crear', $data);
    }

    public function editar($id)
    {
        $persona = $this->personaModel->find($id);

        if (!$persona) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Persona no encontrada");
        }

        $data = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'persona' => $persona,
            'departamentos' => $this->departamentoModel->findAll(),
            'distritos' => $this->distritoModel->findAll(),
        ];

        if ($this->request->isAJAX()) {
            return view('personas/modal_editar', $data);
        }

        return view('personas/editar', $data);
    }

    public function modalCrear($idpersona)
    {
        $persona = $this->personaModel->find($idpersona);
        if (!$persona) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Persona no encontrada.'
            ]);
        }

        $modalidades = $this->modalidadesModel->findAll();
        $origenes    = $this->origenModel->findAll();
        $campanias   = $this->campaniaModel->findAll();
        $distritos   = $this->distritoModel->findAll();
        $personas = $this->personaModel->findAll();

        $data = [
            'persona'     => $persona,
            'modalidades' => $modalidades,
            'origenes'    => $origenes,
            'campanias'   => $campanias,
            'distritos'   => $distritos,
            'personas'    => $personas, 
        ];

        return view('leads/modals', $data);
    }

    public function guardarLead()
    {
        $post = $this->request->getPost();

        // Buscar el origen seleccionado para saber si es campaña o referido
        $origen = $this->origenModel->find($post['idorigen'] ?? 0);
        $tipoOrigen = $origen['tipo'] ?? ''; 

        // Validación base
        $rules = [
            'idpersona' => 'required|integer',
            'idorigen' => 'required|integer',
            'idmodalidad' => 'required|integer',
        ];

        // Validación condicional según tipo de origen
        if ($tipoOrigen === 'campaña') {
            $rules['idcampania'] = 'required|integer';
        } elseif ($tipoOrigen === 'referido') {
            $rules['referido_por'] = 'required|integer';
        }

        // Ejecutar validación
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $leadData = [
                'idpersona' => $post['idpersona'],
                'idorigen' => $post['idorigen'],
                'idusuario' => session()->get('idusuario'),
                'idmodalidad' => $post['idmodalidad'],
                'idcampania' => $tipoOrigen === 'campaña' ? $post['idcampania'] : null,
                'referido_por' => $tipoOrigen === 'referido' ? $post['referido_por'] : null,
                'idusuario_registro' => session()->get('idusuario'), 
                'estado' => 'Convertido',
                'idetapa' => 1,
            ];

            // Guardar campaña o referido según tipo
            if ($tipoOrigen === 'campaña') {
                $leadData['idcampania'] = $post['idcampania'];
            } elseif ($tipoOrigen === 'referido') {
                $leadData['referido_por'] = $post['referido_por'];
            }

            $this->leadModel->insert($leadData);
            $idlead = $this->leadModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lead registrado correctamente.',
                'redirect' => base_url('leads/index'),
                'idlead' => $idlead, // por si luego quieres usarlo
            ]);
            } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ]);
        }
    }

    public function guardar()
    {
        try {
            $idpersona = $this->request->getPost('idpersona');

            // Reglas de validación
            $rules = [
                'dni' => [
                    'rules' => "required|numeric|exact_length[8]|is_unique[personas.dni,idpersona,{$idpersona}]",
                    'errors' => [
                        'required' => 'El DNI es obligatorio.',
                        'numeric' => 'El DNI debe ser numérico.',
                        'exact_length' => 'El DNI debe tener 8 dígitos.',
                        'is_unique' => 'El DNI ya está registrado en otra persona.'
                    ]
                ],
                'nombres' => 'required|min_length[2]',
                'apellidos' => 'required|min_length[2]',
                'correo' => 'permit_empty|valid_email',
                'telefono' => 'permit_empty|numeric|min_length[6]|max_length[15]',
                'iddistrito' => 'required|integer',
            ];

            if (!$this->validate($rules)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            // Datos a guardar
            $data = $this->request->getPost([
                'dni',
                'apellidos',
                'nombres',
                'correo',
                'telefono',
                'direccion',
                'referencias',
                'iddistrito',
            ]);

            if ($idpersona) {
                // Actualizar
                $this->personaModel->update($idpersona, $data);
                return $this->respondUpdated([
                    'success' => true,
                    'message' => 'Persona actualizada correctamente.',
                    'idpersona' => $idpersona,
                ]);
            }

            // Crear nuevo registro
            $id = $this->personaModel->insert($data);
            return $this->respondCreated([
                'success' => true,
                'message' => 'Persona registrada correctamente.',
                'idpersona' => $id,
            ]);

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function BuscadorDni($dni = "")
    {
        if (strlen($dni) !== 8 || !ctype_digit($dni)) {
            return $this->failValidationErrors('El DNI debe tener 8 dígitos numéricos');
        }

        $api_endpoint = "https://api.decolecta.com/v1/reniec/dni?numero=" . $dni;
        $api_token = env('API_DECOLECTA_TOKEN');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_token,
        ]);
        $api_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($api_response === false || $http_code !== 200) {
            $decoded = json_decode($api_response, true);
            return $this->fail($decoded['message'] ?? 'No encontramos a la persona.');
        }

        $decoded_response = json_decode($api_response, true);

        return $this->respond([
            'success' => true,
            'apepaterno' => $decoded_response['first_last_name'] ?? '',
            'apematerno' => $decoded_response['second_last_name'] ?? '',
            'nombres' => $decoded_response['first_name'] ?? '',
        ]);
    }

    public function eliminar($id)
    {
        $tieneLeads = $this->leadModel->where('idpersona', $id)->countAllResults();

        if ($tieneLeads > 0) {
            return $this->failForbidden('No se puede eliminar la persona porque tiene leads asociados.');
        }

        $this->personaModel->delete($id);

        return $this->respondDeleted('Persona eliminada correctamente.');
    }
}
