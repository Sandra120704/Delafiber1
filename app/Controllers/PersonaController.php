<?php

namespace App\Controllers;

use App\Models\CampanaModel;
use App\Models\DepartamentoModel;
use App\Models\DistritoModel;
use App\Models\LeadModel;
use App\Models\ModalidadesModel;
use App\Models\Origen;
use App\Models\PersonaModel;
use CodeIgniter\API\ResponseTrait;

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
                ->orLike('apellidos', $q)  
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
            'persona' => null 
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

            $this->leadModel->insert($leadData);
            $idlead = $this->leadModel->getInsertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lead registrado correctamente.',
                'redirect' => base_url('leads/index'),
                'idlead' => $idlead,
            ]);
        } catch (\Exception $e) {
            // LOG PARA DEBUG
            log_message('error', 'Error guardarLead: ' . $e->getMessage());
            
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

            // Reglas de validación específicas
            $rules = [
                'dni' => [
                    'rules' => $idpersona ? 
                        "required|numeric|exact_length[8]|is_unique[personas.dni,idpersona,{$idpersona}]" : 
                        "required|numeric|exact_length[8]|is_unique[personas.dni]",
                    'errors' => [
                        'required' => 'El DNI es obligatorio.',
                        'numeric' => 'El DNI debe ser numérico.',
                        'exact_length' => 'El DNI debe tener 8 dígitos.',
                        'is_unique' => 'El DNI ya está registrado.'
                    ]
                ],
                'nombres' => [
                    'rules' => 'required|min_length[2]|max_length[100]',
                    'errors' => [
                        'required' => 'Los nombres son obligatorios.',
                        'min_length' => 'Los nombres deben tener al menos 2 caracteres.',
                        'max_length' => 'Los nombres no pueden exceder 100 caracteres.'
                    ]
                ],
                'apellidos' => [
                    'rules' => 'required|min_length[2]|max_length[100]',
                    'errors' => [
                        'required' => 'Los apellidos son obligatorios.',
                        'min_length' => 'Los apellidos deben tener al menos 2 caracteres.',
                        'max_length' => 'Los apellidos no pueden exceder 100 caracteres.'
                    ]
                ],
                'correo' => [
                    'rules' => 'permit_empty|valid_email|max_length[150]',
                    'errors' => [
                        'valid_email' => 'El formato del correo no es válido.',
                        'max_length' => 'El correo no puede exceder 150 caracteres.'
                    ]
                ],
                'telefono' => [
                    'rules' => 'required|numeric|exact_length[9]',
                    'errors' => [
                        'required' => 'El teléfono es obligatorio.',
                        'numeric' => 'El teléfono debe ser numérico.',
                        'exact_length' => 'El teléfono debe tener 9 dígitos.'
                    ]
                ],
                'iddistrito' => [
                    'rules' => 'required|integer|is_not_unique[distritos.iddistrito]',
                    'errors' => [
                        'required' => 'Debe seleccionar un distrito.',
                        'integer' => 'El distrito debe ser válido.',
                        'is_not_unique' => 'El distrito seleccionado no existe.'
                    ]
                ],
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Datos a guardar
            $data = [
                'dni' => $this->request->getPost('dni'),
                'apellidos' => trim($this->request->getPost('apellidos')),
                'nombres' => trim($this->request->getPost('nombres')),
                'correo' => $this->request->getPost('correo') ?: null,
                'telefono' => $this->request->getPost('telefono'),
                'direccion' => $this->request->getPost('direccion') ?: null,
                'referencias' => $this->request->getPost('referencias') ?: null,
                'iddistrito' => $this->request->getPost('iddistrito'),
            ];

            if ($idpersona) {
                // Actualizar
                $success = $this->personaModel->update($idpersona, $data);
                if (!$success) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'No se pudo actualizar la persona.'
                    ]);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Persona actualizada correctamente.',
                    'idpersona' => $idpersona,
                ]);
            }

            // Crear nuevo registro
            $id = $this->personaModel->insert($data);
            
            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo registrar la persona. Verifique los datos.'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Persona registrada correctamente.',
                'idpersona' => $id,
            ]);

        } catch (\Exception $e) {
            // LOG PARA DEBUG
            log_message('error', 'Error en guardar persona: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor. Intente nuevamente.'
            ]);
        }
    }
    public function buscardni($dni = "")
    {
        $dni = $this->request->getGet('q');
        
        if (strlen($dni) !== 8 || !ctype_digit($dni)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'El DNI debe tener 8 dígitos numéricos'
            ]);
        }

        try {
            // Verificar si ya existe en la base de datos local
            $persona = $this->personaModel->where('dni', $dni)->first();
            
            if ($persona) {
                return $this->response->setJSON([
                    'success' => true,
                    'registrado' => true,
                    'DNI' => $persona['dni'],
                    'nombres' => $persona['nombres'],
                    'apepaterno' => explode(' ', $persona['apellidos'])[0] ?? '',
                    'apematerno' => explode(' ', $persona['apellidos'])[1] ?? '',
                    'message' => 'Persona encontrada en la base de datos'
                ]);
            }

            // API DE RENIEC (si tienes token)
            $api_token = env('API_DECOLECTA_TOKEN');
            
            if ($api_token) {
                $api_endpoint = "https://api.decolecta.com/v1/reniec/dni?numero=" . $dni;
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $api_token,
                ]);
                
                $api_response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($api_response !== false && $http_code === 200) {
                    $decoded_response = json_decode($api_response, true);
                    
                    if (isset($decoded_response['first_name'])) {
                        return $this->response->setJSON([
                            'success' => true,
                            'registrado' => false,
                            'apepaterno' => $decoded_response['first_last_name'] ?? '',
                            'apematerno' => $decoded_response['second_last_name'] ?? '',
                            'nombres' => $decoded_response['first_name'] ?? '',
                        ]);
                    }
                }
            }

            // Si no se encontró en API externa
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'No se encontró información para este DNI'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en buscardni: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al consultar el DNI'
            ]);
            
        }
    }

    public function eliminar($id)
    {
        try {
            $tieneLeads = $this->leadModel->where('idpersona', $id)->countAllResults();

            if ($tieneLeads > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se puede eliminar la persona porque tiene leads asociados.'
                ]);
            }

            $success = $this->personaModel->delete($id);
            
            if ($success) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Persona eliminada correctamente.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo eliminar la persona.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error eliminando persona: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor.'
            ]);
        }
    }

    // Metodo de Busqueda de AJAX
    public function buscarAjax()
    {
        $query = $this->request->getGet('q');
        
        if (empty($query)) {
            $personas = $this->personaModel->orderBy('idpersona', 'DESC')->findAll();
        } else {
            $personas = $this->personaModel
                ->like('nombres', $query)
                ->orLike('apellidos', $query)
                ->orLike('dni', $query)
                ->orLike('telefono', $query)
                ->orLike('correo', $query)
                ->findAll();
        }

        return $this->response->setJSON($personas);
    }
}