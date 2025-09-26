<?php

namespace App\Controllers;

use App\Models\CampanaModel;
use App\Models\DepartamentoModel;
use App\Models\DistritoModel;
use App\Models\LeadHistorialModel;
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
        $personas = $q
            ? $this->personaModel->buscarPersonas($q)
            : $this->personaModel->orderBy('idpersona', 'DESC')->paginate($perPage);

        $data = [
            'header' => view('layouts/header'),
            'footer' => view('layouts/footer'),
            'personas' => $personas,
            'pager' => $this->personaModel->pager,
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
        $persona = $this->personaModel->getPersonaConDistrito($id);

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

        return view('leads/leads-modal', $data); // Usa la vista refactorizada
    }

    public function guardarLead()
    {
        // Validar sesión
        if (!session()->has('idusuario')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesión no válida. Inicie sesión nuevamente.'
            ]);
        }

        $post = $this->request->getPost();
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();
            
            // 1. Verificar que la persona existe
            $persona = $this->personaModel->find($post['idpersona'] ?? 0);
            if (!$persona) {
                throw new \Exception('Persona no encontrada');
            }

            // 2. Verificar que no tenga leads activos
            $leadExistente = $this->leadModel
                ->where('idpersona', $persona['idpersona'])
                ->where('estado !=', 'Descartado')
                ->first();
            
            if ($leadExistente) {
                throw new \Exception('Esta persona ya tiene un lead activo');
            }

            // 3. Obtener información del origen
            $origen = $this->origenModel->find($post['idorigen'] ?? 0);
            if (!$origen) {
                throw new \Exception('Origen no válido');
            }

            $tipoOrigen = $origen['tipo'] ?? '';
            $referidoPorId = null;

            // 4. Manejar referidos (crear nuevo o usar existente)
            if ($tipoOrigen === 'referido') {
                $crearReferido = $post['crear_referido'] ?? '0';
                
                if ($crearReferido === '1') {
                    // Crear nuevo referido
                    $referidoPorId = $this->crearNuevoReferido($post);
                    if (!$referidoPorId) {
                        throw new \Exception('Error al crear la persona que refirió');
                    }
                } else {
                    // Usar referido existente
                    $referidoPorId = $post['referido_por'] ?? null;
                    if (!$referidoPorId) {
                        throw new \Exception('Debe seleccionar quien refirió o crear una nueva persona');
                    }
                    
                    // Verificar que el referido existe
                    $referidoExiste = $this->personaModel->find($referidoPorId);
                    if (!$referidoExiste) {
                        throw new \Exception('La persona que refirió no existe');
                    }
                }
            }

            // 5. Validar campos según tipo de origen
            $rules = $this->getValidationRulesByOrigin($tipoOrigen, $post);
            
            if (!$this->validate($rules)) {
                $errors = $this->validator->getErrors();
                throw new \Exception('Errores de validación: ' . implode(', ', $errors));
            }

            // 6. Crear el lead
            $leadData = [
                'idpersona' => $post['idpersona'],
                'idorigen' => $post['idorigen'],
                'idusuario' => session()->get('idusuario'),
                'idmodalidad' => $post['idmodalidad'],
                'idcampania' => $tipoOrigen === 'campaña' ? ($post['idcampania'] ?? null) : null,
                'referido_por' => $referidoPorId,
                'idusuario_registro' => session()->get('idusuario'),
                'estado' => 'Nuevo',
                'idetapa' => 1,
                'observaciones' => $post['observaciones'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $idlead = $this->leadModel->insert($leadData);
            
            if (!$idlead) {
                throw new \Exception('Error al crear el lead');
            }

            // 7. Crear entrada en el historial
            $this->crearEntradaHistorial($idlead, $post, $tipoOrigen, $referidoPorId);

            // 8. Actualizar estadísticas de campaña o referido
            $this->actualizarEstadisticas($tipoOrigen, $post, $referidoPorId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lead creado exitosamente con todos los detalles',
                'idlead' => $idlead,
                'data' => [
                    'tipo_origen' => $tipoOrigen,
                    'referido_creado' => ($post['crear_referido'] ?? '0') === '1',
                    'tiene_observaciones' => !empty($post['observaciones'])
                ]
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error en guardarLead: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function crearNuevoReferido($post)
    {
        try {
            // Validar datos del nuevo referido
            $referidoData = [
                'dni' => $post['referido_dni'] ?? '',
                'nombres' => trim($post['referido_nombres'] ?? ''),
                'apellidos' => trim($post['referido_apellidos'] ?? ''),
                'telefono' => $post['referido_telefono'] ?? '',
                'correo' => $post['referido_correo'] ?? null,
                'iddistrito' => 1, // Distrito por defecto - puedes cambiarlo
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Verificar que el DNI no exista
            if ($referidoData['dni']) {
                $existente = $this->personaModel->where('dni', $referidoData['dni'])->first();
                if ($existente) {
                    throw new \Exception('El DNI del referido ya existe en la base de datos');
                }
            }

            $idReferido = $this->personaModel->insert($referidoData);
            
            if (!$idReferido) {
                throw new \Exception('No se pudo crear la persona que refirió');
            }

            return $idReferido;

        } catch (\Exception $e) {
            log_message('error', 'Error creando referido: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getValidationRulesByOrigin($tipoOrigen, $post)
    {
        $rules = [
            'idpersona' => 'required|integer',
            'idorigen' => 'required|integer',
            'idmodalidad' => 'required|integer',
        ];

        if ($tipoOrigen === 'campaña') {
            $rules['idcampania'] = 'required|integer';
        } elseif ($tipoOrigen === 'referido') {
            $crearReferido = $post['crear_referido'] ?? '0';
            
            if ($crearReferido === '1') {
                // Validar campos de nuevo referido
                $rules['referido_dni'] = 'required|exact_length[8]|numeric';
                $rules['referido_nombres'] = 'required|min_length[2]|max_length[100]';
                $rules['referido_apellidos'] = 'required|min_length[2]|max_length[100]';
                $rules['referido_telefono'] = 'permit_empty|exact_length[9]|numeric';
                $rules['referido_correo'] = 'permit_empty|valid_email|max_length[150]';
            } else {
                $rules['referido_por'] = 'required|integer';
            }
        }

        return $rules;
    }

    private function crearEntradaHistorial($idlead, $post, $tipoOrigen, $referidoPorId)
    {
        try {
            // Crear descripción detallada para el historial
            $descripcion = "Lead creado desde origen: " . $this->obtenerNombreOrigen($post['idorigen']);
            
            if ($tipoOrigen === 'campaña') {
                $campania = $this->campaniaModel->find($post['idcampania']);
                $descripcion .= " | Campaña: " . ($campania['nombre'] ?? 'N/A');
            } elseif ($tipoOrigen === 'referido' && $referidoPorId) {
                $referido = $this->personaModel->find($referidoPorId);
                $descripcion .= " | Referido por: " . ($referido['nombres'] ?? '') . " " . ($referido['apellidos'] ?? '');
                
                if ($post['crear_referido'] === '1') {
                    $descripcion .= " (persona creada automáticamente)";
                }
            }

            if (!empty($post['observaciones'])) {
                $descripcion .= " | Observaciones: " . substr($post['observaciones'], 0, 200);
            }

            $historialData = [
                'idlead' => $idlead,
                'idusuario' => session()->get('idusuario'),
                'accion' => 'lead_creado',
                'descripcion' => $descripcion,
                'etapa_anterior' => null,
                'etapa_nueva' => 1,
                'fecha' => date('Y-m-d H:i:s')
            ];

            // Asumiendo que tienes una tabla lead_historial
            $historialModel = new LeadHistorialModel();
            return $historialModel->insert($historialData);

        } catch (\Exception $e) {
            log_message('error', 'Error creando historial: ' . $e->getMessage());
            // No lanzar excepción para no interrumpir el proceso principal
            return false;
        }
    }

    private function actualizarEstadisticas($tipoOrigen, $post, $referidoPorId)
    {
        try {
            if ($tipoOrigen === 'campaña' && !empty($post['idcampania'])) {
                // Incrementar contador de leads de la campaña
                $this->campaniaModel->set('leads_generados', 'leads_generados + 1', false)
                                   ->where('idcampania', $post['idcampania'])
                                   ->update();
            } elseif ($tipoOrigen === 'referido' && $referidoPorId) {
                // Actualizar estadísticas de referidos
                $this->personaModel->set('total_referidos', 'total_referidos + 1', false)
                                  ->where('idpersona', $referidoPorId)
                                  ->update();
            }
        } catch (\Exception $e) {
            log_message('error', 'Error actualizando estadísticas: ' . $e->getMessage());
            // No interrumpir el proceso principal
        }
    }

    private function obtenerNombreOrigen($idorigen)
    {
        try {
            $origen = $this->origenModel->find($idorigen);
            return $origen['nombre'] ?? 'Origen desconocido';
        } catch (\Exception $e) {
            return 'Origen desconocido';
        }
    }

    // Método adicional para obtener detalles del lead creado
    public function obtenerDetallesLead($idlead)
    {
        try {
            $lead = $this->leadModel->select('leads.*, personas.nombres, personas.apellidos, personas.dni')
                                   ->join('personas', 'personas.idpersona = leads.idpersona')
                                   ->find($idlead);
            
            if (!$lead) {
                return $this->response->setJSON(['success' => false, 'message' => 'Lead no encontrado']);
            }

            // Obtener información adicional según el origen
            $detalles = ['lead' => $lead];
            
            if ($lead['idcampania']) {
                $detalles['campania'] = $this->campaniaModel->find($lead['idcampania']);
            }
            
            if ($lead['referido_por']) {
                $detalles['referido_por'] = $this->personaModel->find($lead['referido_por']);
            }

            // Obtener historial
            $historialModel = new LeadHistorialModel();
            $detalles['historial'] = $historialModel->where('idlead', $idlead)
                                                   ->orderBy('fecha', 'DESC')
                                                   ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $detalles
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo detalles del lead: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener detalles del lead'
            ]);
        }
    }

    public function guardar()
    {
        try {
            $idpersona = $this->request->getPost('idpersona');
            $dni = $this->request->getPost('dni');

            // Verificar si el DNI ya existe antes de crear
            if (!$idpersona && $dni) {
                $personaExistente = $this->personaModel->where('dni', $dni)->first();
                if ($personaExistente) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'El DNI ya está registrado. No se puede crear otra persona con el mismo DNI.'
                    ]);
                }
            }

            // Reglas de validación específicas
            $rules = [
                'dni' => [
                    'rules' => $idpersona
                        ? "required|numeric|exact_length[8]|is_unique[personas.dni,idpersona,{$idpersona}]"
                        : "required|numeric|exact_length[8]|is_unique[personas.dni]",
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

            // Validar antes de guardar
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

            // Verificación extra antes del insert (por si el modelo no lo detecta)
            if (!$idpersona && $dni) {
                $personaExistente = $this->personaModel->where('dni', $dni)->first();
                if ($personaExistente) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'El DNI ya está registrado. No se puede crear otra persona con el mismo DNI.'
                    ]);
                }
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
            // Si el error es por duplicado, muestra mensaje amigable
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El DNI ya está registrado. No se puede crear otra persona con el mismo DNI.'
                ]);
            }
            log_message('error', 'Error en guardar persona: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor. ' . $e->getMessage()
            ]);
        }
    }
    public function buscardni($dni = "")
    {
        $dni = $this->request->getGet('q') ?: $dni;
        $dni = preg_replace('/\D/', '', $dni); // Solo números

        if (strlen($dni) !== 8) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'El DNI debe tener exactamente 8 dígitos numéricos'
            ]);
        }

        try {
            $persona = $this->personaModel->where('dni', $dni)->first();
            if ($persona) {
                $apellidos = isset($persona['apellidos']) ? explode(' ', trim($persona['apellidos']), 2) : ['', ''];
                return $this->response->setJSON([
                    'success' => true,
                    'registrado' => true,
                    'DNI' => $persona['dni'],
                    'nombres' => $persona['nombres'] ?? '',
                    'apepaterno' => $apellidos[0] ?? '',
                    'apematerno' => $apellidos[1] ?? '',
                    'message' => 'Persona encontrada en la base de datos local'
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
                'message' => 'Error interno del servidor'
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
        $query = trim($query);

        try {
            if (empty($query)) {
                $personas = $this->personaModel
                    ->select('idpersona, nombres, apellidos, dni, telefono, correo, direccion')
                    ->orderBy('idpersona', 'DESC')
                    ->limit(20)
                    ->findAll();
            } else {
                $personas = $this->personaModel
                    ->select('idpersona, nombres, apellidos, dni, telefono, correo, direccion')
                    ->groupStart()
                        ->like('nombres', $query)
                        ->orLike('apellidos', $query)
                        ->orLike('dni', $query)
                        ->orLike('telefono', $query)
                        ->orLike('correo', $query)
                    ->groupEnd()
                    ->orderBy('idpersona', 'DESC')
                    ->limit(50)
                    ->findAll();
            }

            // Sanitizar datos de salida
            $personas = array_map(function($persona) {
                return [
                    'idpersona' => (int)$persona['idpersona'],
                    'nombres' => htmlspecialchars($persona['nombres'], ENT_QUOTES, 'UTF-8'),
                    'apellidos' => htmlspecialchars($persona['apellidos'], ENT_QUOTES, 'UTF-8'),
                    'dni' => htmlspecialchars($persona['dni'], ENT_QUOTES, 'UTF-8'),
                    'telefono' => htmlspecialchars($persona['telefono'], ENT_QUOTES, 'UTF-8'),
                    'correo' => htmlspecialchars($persona['correo'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'direccion' => htmlspecialchars($persona['direccion'] ?? '', ENT_QUOTES, 'UTF-8')
                ];
            }, $personas);

            return $this->response->setJSON($personas);

        } catch (\Exception $e) {
            log_message('error', 'Error en buscarAjax: ' . $e->getMessage());
            return $this->response->setJSON([]);
        }
    }
}