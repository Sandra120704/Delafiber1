<?php

namespace App\Controllers;

use App\Models\LeadModel;
use App\Models\PersonaModel;
use App\Models\CampanaModel;
use App\Models\ModalidadesModel;
use App\Models\Origen;
use App\Models\EtapaModel;
use App\Models\TareaModel;
use App\Models\SeguimientoModel;
use App\Models\DistritoModel;

class LeadController extends BaseController
{
    protected $leadModel;
    protected $personaModel;
    protected $campanaModel;
    protected $modalidadesModel;
    protected $origenModel;
    protected $etapaModel;
    protected $tareaModel;
    protected $seguimientoModel;
    protected $distritoModel;

    public function __construct()
    {
        $this->leadModel = new LeadModel();
        $this->personaModel = new PersonaModel();
        $this->campanaModel = new CampanaModel();
        $this->modalidadesModel = new ModalidadesModel();
        $this->origenModel = new Origen();
        $this->etapaModel = new EtapaModel();
        $this->tareaModel = new TareaModel();
        $this->seguimientoModel = new SeguimientoModel();
        $this->distritoModel = new DistritoModel();
    }

    public function index()
    {
        $data['leads'] = $this->leadModel->getLeadsConTodo();
        $data['etapas'] = $this->etapaModel->findAll();
        $data['campanias'] = $this->campanaModel->findAll();
        $data['modalidades'] = $this->modalidadesModel->findAll();
        $data['origenes'] = $this->origenModel->findAll();
        $data['distritos'] = $this->distritoModel->findAll();
        $data['leadsPorEtapa'] = $this->leadModel->getLeadsPorEtapa();

        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');

        return view('leads/index', $data);
    }

    public function modalCrear($idpersona)
    {
        $persona = $this->personaModel->find($idpersona);
        if (!$persona) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Persona no encontrada'
            ]);
        }

        return view('leads/modals', [
            'persona' => $persona,
            'modalidades' => $this->modalidadesModel->findAll(),
            'origenes' => $this->origenModel->findAll(),
            'campanias' => $this->campanaModel->findAll(),
        ]);
    }

    public function guardar()
    {
        try {
            $post = $this->request->getPost();
            $idpersona = $post['idpersona'] ?? null;

            // Si no hay idpersona, crear una nueva persona
            if (!$idpersona) {
                // Validar datos requeridos para nueva persona
                if (empty($post['nombres']) || empty($post['apellidos']) || empty($post['telefono'])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Nombres, apellidos y teléfono son campos requeridos.'
                    ]);
                }

                // Validar DNI si se proporciona
                if (!empty($post['dni'])) {
                    if (strlen($post['dni']) !== 8 || !is_numeric($post['dni'])) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'El DNI debe tener exactamente 8 dígitos numéricos.'
                        ]);
                    }

                    // Verificar si ya existe persona con ese DNI
                    $personaExistente = $this->personaModel->where('dni', $post['dni'])->first();
                    if ($personaExistente) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Ya existe una persona registrada con este DNI.'
                        ]);
                    }
                }

                // Validar teléfono
                if (strlen($post['telefono']) !== 9 || !is_numeric($post['telefono'])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'El teléfono debe tener exactamente 9 dígitos numéricos.'
                    ]);
                }

                // Crear nueva persona
                $dataPersona = [
                    'dni' => $post['dni'] ?? null,
                    'nombres' => trim($post['nombres']),
                    'apellidos' => trim($post['apellidos']),
                    'telefono' => $post['telefono'],
                    'correo' => $post['correo'] ?? null,
                    'direccion' => $post['direccion'] ?? null,
                    'iddistrito' => !empty($post['iddistrito']) ? $post['iddistrito'] : null,
                    'referencias' => $post['referencias'] ?? null,
                ];

                $idpersona = $this->personaModel->insert($dataPersona);
                
                if (!$idpersona) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Error al registrar la persona.'
                    ]);
                }
            }

            // Verificar si ya existe un lead para esta persona
            if ($this->leadModel->where('idpersona', $idpersona)->first()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Esta persona ya está registrada como Lead.'
                ]);
            }

            // Obtener etapa inicial
            $etapaInicial = $this->etapaModel->orderBy('orden', 'ASC')->first();
            if (!$etapaInicial) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No hay etapas configuradas en el sistema.'
                ]);
            }

            // Crear el lead
            $dataLead = [
                'idpersona' => $idpersona,
                'idcampania' => !empty($post['idcampania']) ? $post['idcampania'] : null,
                'idmodalidad' => !empty($post['idmodalidad']) ? $post['idmodalidad'] : null,
                'idorigen' => !empty($post['idorigen']) ? $post['idorigen'] : null,
                'referido_por' => $post['referido_por'] ?? null,
                'estado' => 'Nuevo',
                'idetapa' => $etapaInicial['idetapa'],
                'idusuario' => session('idusuario') ?? 1,
                'idusuario_registro' => session('idusuario') ?? 1,
            ];

            $idlead = $this->leadModel->insert($dataLead);

            if ($idlead) {
                $persona = $this->personaModel->find($idpersona);
                if ($persona) {
                    $persona = (array) $persona;  // Convertir a array
                    $persona['idetapa'] = $dataLead['idetapa'];
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Lead registrado correctamente.',
                    'idlead'  => $idlead,
                    'persona' => $persona
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al registrar el lead.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en LeadController::guardar: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    public function detalle($idlead)
    {
        $lead = $this->leadModel
            ->select('leads.*, personas.nombres, personas.apellidos, personas.telefono, personas.correo, usuarios.usuario as usuario')
            ->join('personas', 'personas.idpersona = leads.idpersona', 'left')
            ->join('usuarios', 'usuarios.idusuario = leads.idusuario', 'left')
            ->where('leads.idlead', $idlead)
            ->first();

        if (!$lead) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lead no encontrado'
            ]);
        }

        $tareas = $this->tareaModel->where('idlead', $idlead)->orderBy('fecha_inicio','ASC')->findAll();
        $seguimientos = $this->seguimientoModel->where('idlead', $idlead)->orderBy('fecha','ASC')->findAll();
        $modalidades = $this->modalidadesModel->findAll();

        return $this->response->setJSON([
            'success' => true,
            'lead' => $lead,
            'tareas' => $tareas,
            'seguimientos' => $seguimientos,
            'modalidades' => $modalidades
        ]);
    }

    public function actualizarEtapa()
    {
        try {
            $idlead = $this->request->getPost('idlead');
            $idetapa = $this->request->getPost('idetapa');

            if (!$idlead || !$idetapa) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Datos incompletos: ID de lead e ID de etapa son requeridos'
                ]);
            }

            // Validar que sean números válidos
            if (!is_numeric($idlead) || !is_numeric($idetapa)) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Los IDs deben ser números válidos'
                ]);
            }

            $this->leadModel->actualizarEtapa($idlead, $idetapa);

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Etapa actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error actualizando etapa: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error al actualizar la etapa: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminar()
    {
        $idlead = $this->request->getPost('idlead');
        if (!$idlead) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID de Lead no proporcionado']);
        }

        $this->leadModel->update($idlead, ['estado' => 'Descartado']);

        return $this->response->setJSON(['success' => true, 'message' => 'Lead descartado correctamente']);
    }

    public function guardarSeguimiento()
    {
        $idlead = $this->request->getPost('idlead');
        $idmodalidad = $this->request->getPost('idmodalidad');
        $comentario = $this->request->getPost('comentario');

        if (!$idlead || !$idmodalidad || !$comentario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos incompletos para el seguimiento'
            ]);
        }

        $id = $this->seguimientoModel->insert([
            'idlead'      => $idlead,
            'idusuario'   => session()->get('idusuario'),
            'idmodalidad' => $idmodalidad,
            'comentario'  => $comentario,
            'fecha'       => date('Y-m-d H:i:s')
        ]);

        if ($id) {
            return $this->response->setJSON([
                'success' => true,
                'seguimiento' => [
                    'id'        => $id,
                    'comentario'=> $comentario,
                    'fecha'     => date('d/m/Y H:i')
                ]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al guardar seguimiento'
        ]);
    }

    public function guardarTarea()
    {
        $idlead = $this->request->getPost('idlead');
        $descripcion = $this->request->getPost('descripcion');
        $fechaInicio = $this->request->getPost('fecha_inicio');
        $tipoTarea = $this->request->getPost('tipo') ?? 'seguimiento';
        $prioridad = $this->request->getPost('prioridad') ?? 'media';

        // Validaciones básicas
        if (!$idlead || !$descripcion) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos incompletos para la tarea'
            ]);
        }

        // Usar descripción como título si es corto, sino crear título resumido
        $titulo = strlen(trim($descripcion)) <= 50 
            ? trim($descripcion)
            : substr(trim($descripcion), 0, 47) . '...';

        // Preparar fechas
        $fechaInicioFormatted = $fechaInicio ? date('Y-m-d', strtotime($fechaInicio)) : date('Y-m-d');
        $fechaFinFormatted = $fechaInicio ? date('Y-m-d', strtotime($fechaInicio)) : date('Y-m-d');
        $fechaVencimiento = $fechaInicio ? date('Y-m-d H:i:s', strtotime($fechaInicio)) : date('Y-m-d H:i:s', strtotime('+1 day'));

        $dataTarea = [
            'idlead'            => $idlead,
            'idusuario'         => session()->get('idusuario') ?? 1,
            'titulo'            => $titulo,
            'descripcion'       => trim($descripcion),
            'tipo_tarea'        => $tipoTarea,
            'prioridad'         => $prioridad,
            'fecha_inicio'      => $fechaInicioFormatted,
            'fecha_fin'         => $fechaFinFormatted,
            'fecha_vencimiento' => $fechaVencimiento,
            'estado'            => 'Pendiente'
        ];

        try {
            $id = $this->tareaModel->insert($dataTarea);

            if ($id) {
                return $this->response->setJSON([
                    'success' => true,
                    'tarea' => [
                        'id'             => $id,
                        'titulo'         => $titulo,
                        'descripcion'    => $descripcion,
                        'estado'         => 'Pendiente',
                        'prioridad'      => $prioridad,
                        'fecha_inicio'   => $fechaInicioFormatted
                    ],
                    'message' => 'Tarea creada exitosamente'
                ]);
            }
        } catch (\Exception $e) {
            // Log del error para debugging
            log_message('error', 'Error creando tarea: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al guardar la tarea: ' . $e->getMessage()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error desconocido al guardar la tarea'
        ]);
    }

    public function obtenerTareas($idlead)
    {
        try {
            $tareas = $this->tareaModel->where('idlead', $idlead)->orderBy('fecha_inicio', 'ASC')->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'tareas' => $tareas
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener las tareas'
            ]);
        }
    }

    public function actualizarEstadoTarea()
    {
        $idtarea = $this->request->getPost('idtarea');
        $estado = $this->request->getPost('estado');

        if (!$idtarea || !$estado) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
        }

        $datosActualizar = ['estado' => $estado];
        
        // Si se marca como completada, establecer fecha de completado
        if ($estado === 'Completada') {
            $datosActualizar['fecha_completado'] = date('Y-m-d H:i:s');
        } else {
            $datosActualizar['fecha_completado'] = null;
        }

        $resultado = $this->tareaModel->update($idtarea, $datosActualizar);

        if ($resultado) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estado de tarea actualizado'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al actualizar el estado de la tarea'
        ]);
    }

    public function convertirALead($idpersona)
    {
        $persona = $this->personaModel->find($idpersona);
        if (!$persona) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Persona no encontrada.'
            ]);
        }

        $existeLead = $this->leadModel->where('idpersona', $idpersona)->first();
        if ($existeLead) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El lead para esta persona ya existe.'
            ]);
        }

        $dataLead = [
            'idpersona'         => $idpersona,
            'estado'            => 'Nuevo',
            'idetapa'           => 1,
            'idusuario_registro'=> session('idusuario'),
            'idusuario'         => session('idusuario')
        ];

        $idlead = $this->leadModel->insert($dataLead);

        if ($idlead) {
            $persona['idetapa'] = $dataLead['idetapa'];

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lead creado correctamente.',
                'idlead'  => $idlead,
                'persona' => $persona
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al crear lead.'
        ]);
    }

    public function validar($idpersona)
    {
        $existe = $this->leadModel->where('idpersona', $idpersona)->first() ? true : false;
        return $this->response->setJSON(['exists' => $existe]);
    }

    public function moverEtapa()
    {
        try {
            $idlead = $this->request->getPost('idlead');
            $nuevaEtapa = $this->request->getPost('nueva_etapa');
            $etapaAnterior = $this->request->getPost('etapa_anterior');

            // Validar datos
            if (!$idlead || !$nuevaEtapa) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Datos incompletos'
                ]);
            }

            // Verificar que el lead existe
            $lead = $this->leadModel->find($idlead);
            if (!$lead) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Lead no encontrado'
                ]);
            }

            // Actualizar la etapa del lead
            $resultado = $this->leadModel->update($idlead, [
                'idetapa' => $nuevaEtapa,
                'fecha_modificacion' => date('Y-m-d H:i:s')
            ]);

            if ($resultado) {
                // Registrar actividad en seguimiento
                $this->seguimientoModel->insert([
                    'idlead' => $idlead,
                    'idusuario' => session()->get('idusuario') ?? 1, 
                    'idmodalidad' => 1, 
                    'nota' => "Lead movido de etapa {$etapaAnterior} a etapa {$nuevaEtapa}",
                    'fecha' => date('Y-m-d H:i:s')
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Lead movido exitosamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar el lead'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error moviendo lead: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }
}
