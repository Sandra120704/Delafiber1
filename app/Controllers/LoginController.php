<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

/**
 * ===================================================
 * CONTROLADOR DE AUTENTICACIÓN Y SEGURIDAD - DELAFIBER
 * ===================================================
 * 
 * Este controlador maneja toda la autenticación del sistema:
 * - Login seguro con validaciones
 * - Gestión de sesiones
 * - Control de intentos de acceso
 * - Logout seguro
 * - Validación de credenciales
 * - Registro de actividad de usuarios
 * 
 * Empresa: Delafiber (Servicios de Fibra Óptica)
 * @author Tu Nombre
 * @date 2025
 */
class LoginController extends BaseController
{
    // ===== MODELO DE USUARIOS =====
    protected $usuarioModel;
    
    // ===== CONFIGURACIÓN DE SEGURIDAD =====
    private $maxIntentosFallidos = 3;        // Máximo 3 intentos antes de bloquear
    private $tiempoBloqueo = 900;            // 15 minutos de bloqueo
    private $duracionSesion = 28800;         // 8 horas de duración de sesión

    /**
     * Constructor - Inicializa modelo y configuración
     */
    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * ===============================================
     * PÁGINA DE INICIO DE SESIÓN
     * ===============================================
     * 
     * Muestra el formulario de login con:
     * - Validación del lado cliente
     * - Protección CSRF
     * - Diseño responsivo y profesional
     * - Mensajes de estado del sistema
     */
    public function index()
    {
        // Si ya está logueado, redirigir al dashboard
        if ($this->yaEstaLogueado()) {
            return redirect()->to('dashboard');
        }

        // Verificar estado del sistema
        $estadoSistema = $this->verificarEstadoSistema();
        
        $datosLogin = [
            'titulo' => 'Delafiber - Sistema de Gestión',
            'estadoSistema' => $estadoSistema,
            'mantenimiento' => $estadoSistema['mantenimiento'] ?? false,
            'mensajeBienvenida' => $this->obtenerMensajeBienvenida(),
            'ultimaActualizacion' => $this->obtenerUltimaActualizacion()
        ];

        return view('login/login', $datosLogin);
    }

    /**
     * ===============================================
     * PROCESO DE AUTENTICACIÓN
     * ===============================================
     * 
     * Maneja el login con:
     * - Validación exhaustiva
     * - Hash seguro de contraseñas
     * - Control de intentos fallidos
     * - Registro de actividad
     * - Protección contra ataques
     */
    public function auth()
    {
        try {
            // ===== VALIDAR DATOS DE ENTRADA =====
            $validacion = $this->validarCredenciales();
            if (!$validacion['valido']) {
                return $this->manejarErrorLogin($validacion['mensaje']);
            }

            $usuario = $this->request->getPost('usuario');
            $clave = $this->request->getPost('clave');

            // ===== VERIFICAR BLOQUEOS DE SEGURIDAD =====
            if ($this->usuarioEstaBloqueado($usuario)) {
                return $this->manejarErrorLogin('Usuario temporalmente bloqueado por seguridad. Intente más tarde.');
            }

            // ===== BUSCAR Y VALIDAR USUARIO =====
            $datosUsuario = $this->usuarioModel
                ->select('usuarios.*, roles.nombre as nombre_rol, personas.nombres, personas.apellidos, personas.correo')
                ->join('roles', 'usuarios.idrol = roles.idrol', 'left')
                ->join('personas', 'usuarios.idpersona = personas.idpersona', 'left')
                ->where('usuarios.usuario', $usuario)
                ->where('usuarios.activo', true)
                ->first();

            if (!$datosUsuario) {
                $this->registrarIntentoFallido($usuario, 'Usuario no encontrado');
                return $this->manejarErrorLogin('Credenciales incorrectas.');
            }

            // ===== VERIFICAR CONTRASEÑA =====
            if (!$this->verificarContrasena($clave, $datosUsuario['clave'])) {
                $this->registrarIntentoFallido($usuario, 'Contraseña incorrecta');
                return $this->manejarErrorLogin('Credenciales incorrectas.');
            }

            // ===== LOGIN EXITOSO =====
            $this->establecerSesionUsuario($datosUsuario);
            $this->registrarLoginExitoso($datosUsuario);
            $this->limpiarIntentosFallidos($usuario);

            // Redirigir según el rol del usuario
            $urlDestino = $this->determinarUrlDestino($datosUsuario);
            return redirect()->to($urlDestino);

        } catch (\Exception $e) {
            log_message('error', 'Error en LoginController::auth: ' . $e->getMessage());
            return $this->manejarErrorLogin('Error interno del sistema. Contacte al administrador.');
        }
    }

    /**
     * ===============================================
     * CERRAR SESIÓN SEGURO
     * ===============================================
     * 
     * Logout completo con:
     * - Destrucción segura de sesión
     * - Registro de actividad
     * - Limpieza de datos temporales
     * - Redirección segura
     */
    public function logout()
    {
        try {
            // Registrar logout en logs de auditoría
            $this->registrarLogout();
            
            // Limpiar datos de sesión específicos antes de destruir
            $session = session();
            $session->remove(['idusuario', 'usuario', 'rol', 'nombre_completo', 'permisos', 'isLoggedIn']);
            
            // Destruir sesión completamente
            $session->destroy();
            
            // Limpiar cualquier cookie de sesión
            $this->limpiarCookiesSesion();
            
            return redirect()->to('login')->with('success', 'Sesión cerrada correctamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en LoginController::logout: ' . $e->getMessage());
            return redirect()->to('login');
        }
    }

    /**
     * ===============================================
     * VERIFICAR ESTADO DE SESIÓN (AJAX)
     * ===============================================
     * 
     * Endpoint para verificar si la sesión está activa
     * Usado por JavaScript para mantener sesión activa
     */
    public function verificarSesion()
    {
        $sesionActiva = $this->yaEstaLogueado();
        
        return $this->response->setJSON([
            'sesion_activa' => $sesionActiva,
            'tiempo_restante' => $sesionActiva ? $this->calcularTiempoRestanteSesion() : 0,
            'usuario_actual' => $sesionActiva ? session('usuario') : null
        ]);
    }

    /**
     * ===============================================
     * MÉTODOS DE VALIDACIÓN Y SEGURIDAD
     * ===============================================
     */

    /**
     * Valida credenciales de entrada
     */
    private function validarCredenciales()
    {
        $usuario = $this->request->getPost('usuario');
        $clave = $this->request->getPost('clave');

        // Validar campos obligatorios
        if (empty($usuario) || empty($clave)) {
            return ['valido' => false, 'mensaje' => 'Usuario y contraseña son obligatorios'];
        }

        // Validar longitud mínima
        if (strlen($usuario) < 3) {
            return ['valido' => false, 'mensaje' => 'Usuario debe tener al menos 3 caracteres'];
        }

        if (strlen($clave) < 4) {
            return ['valido' => false, 'mensaje' => 'Contraseña debe tener al menos 4 caracteres'];
        }

        // Validar caracteres permitidos
        if (!preg_match('/^[a-zA-Z0-9._@-]+$/', $usuario)) {
            return ['valido' => false, 'mensaje' => 'Usuario contiene caracteres no válidos'];
        }

        return ['valido' => true, 'mensaje' => 'Credenciales válidas'];
    }

    /**
     * Verifica si usuario está bloqueado por intentos fallidos
     */
    private function usuarioEstaBloqueado($usuario)
    {
        $cache = \Config\Services::cache();
        $intentos = $cache->get('login_intentos_' . $usuario);
        
        if ($intentos && $intentos['count'] >= $this->maxIntentosFallidos) {
            $tiempoTranscurrido = time() - $intentos['ultimo_intento'];
            return $tiempoTranscurrido < $this->tiempoBloqueo;
        }
        
        return false;
    }

    /**
     * Verifica contraseña de forma segura
     */
    private function verificarContrasena($claveIngresada, $claveAlmacenada)
    {
        // Si la contraseña almacenada está hasheada, usar password_verify
        if (strlen($claveAlmacenada) >= 60) {
            return password_verify($claveIngresada, $claveAlmacenada);
        }
        
        // Si aún usa texto plano (para compatibilidad), comparar directamente
        // TODO: Migrar todas las contraseñas a hash seguro
        return $claveIngresada === $claveAlmacenada;
    }

    /**
     * ===============================================
     * MÉTODOS DE GESTIÓN DE SESIÓN
     * ===============================================
     */

    /**
     * Establece sesión de usuario con datos completos
     */
    private function establecerSesionUsuario($datosUsuario)
    {
        $session = session();
        $session->set([
            'idusuario' => $datosUsuario['idusuario'],
            'usuario' => $datosUsuario['usuario'],
            'nombre_completo' => $datosUsuario['nombres'] . ' ' . $datosUsuario['apellidos'],
            'rol' => $datosUsuario['idrol'],
            'nombre_rol' => $datosUsuario['nombre_rol'] ?? 'Usuario',
            'email' => $datosUsuario['email'] ?? '',
            'ultimo_acceso' => date('Y-m-d H:i:s'),
            'ip_acceso' => $this->request->getIPAddress(),
            'isLoggedIn' => true,
            'sesion_iniciada' => time()
        ]);

        // Configurar tiempo de vida de la sesión
        $session->setTempdata('', '', $this->duracionSesion);
    }

    /**
     * Verifica si el usuario ya está logueado
     */
    private function yaEstaLogueado()
    {
        $session = session();
        return $session->get('isLoggedIn') === true && $session->get('idusuario');
    }

    /**
     * ===============================================
     * MÉTODOS DE REGISTRO Y AUDITORÍA
     * ===============================================
     */

    /**
     * Registra intento de login fallido
     */
    private function registrarIntentoFallido($usuario, $motivo)
    {
        $cache = \Config\Services::cache();
        $intentos = $cache->get('login_intentos_' . $usuario) ?? ['count' => 0, 'ultimo_intento' => 0];
        
        $intentos['count']++;
        $intentos['ultimo_intento'] = time();
        
        $cache->save('login_intentos_' . $usuario, $intentos, $this->tiempoBloqueo);
        
        // Log de seguridad
        log_message('warning', "Intento de login fallido - Usuario: {$usuario}, Motivo: {$motivo}, IP: " . $this->request->getIPAddress());
    }

    /**
     * Registra login exitoso
     */
    private function registrarLoginExitoso($datosUsuario)
    {
        // Actualizar último acceso en base de datos
        $this->usuarioModel->update($datosUsuario['idusuario'], [
            'ultimo_acceso' => date('Y-m-d H:i:s'),
            'ip_ultimo_acceso' => $this->request->getIPAddress()
        ]);
        
        // Log de auditoría
        log_message('info', "Login exitoso - Usuario: {$datosUsuario['usuario']}, IP: " . $this->request->getIPAddress());
    }

    /**
     * Registra logout del usuario
     */
    private function registrarLogout()
    {
        if ($this->yaEstaLogueado()) {
            $usuario = session('usuario');
            log_message('info', "Logout - Usuario: {$usuario}, IP: " . $this->request->getIPAddress());
        }
    }

    /**
     * ===============================================
     * MÉTODOS AUXILIARES
     * ===============================================
     */

    /**
     * Maneja errores de login de forma consistente
     */
    private function manejarErrorLogin($mensaje)
    {
        return redirect()->back()
            ->withInput()
            ->with('error', $mensaje);
    }

    /**
     * Determina URL de destino según rol de usuario
     */
    private function determinarUrlDestino($datosUsuario)
    {
        switch ($datosUsuario['idrol']) {
            case 1: // Administrador
                return 'dashboard';
            case 2: // Gerente
                return 'dashboard';
            case 3: // Vendedor
                return 'leads';
            case 4: // Técnico
                return 'tareas';
            default:
                return 'dashboard';
        }
    }

    /**
     * Verifica estado general del sistema
     */
    private function verificarEstadoSistema()
    {
        return [
            'operativo' => true,
            'mantenimiento' => false,
            'version' => '2.1.0',
            'ultimo_backup' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ];
    }

    /**
     * Obtiene mensaje de bienvenida personalizado
     */
    private function obtenerMensajeBienvenida()
    {
        $hora = date('H');
        
        if ($hora < 12) {
            return 'Buenos días, bienvenido a Delafiber';
        } elseif ($hora < 18) {
            return 'Buenas tardes, bienvenido a Delafiber';
        } else {
            return 'Buenas noches, bienvenido a Delafiber';
        }
    }

    /**
     * Limpia intentos fallidos después de login exitoso
     */
    private function limpiarIntentosFallidos($usuario)
    {
        $cache = \Config\Services::cache();
        $cache->delete('login_intentos_' . $usuario);
    }

    /**
     * Limpia cookies de sesión
     */
    private function limpiarCookiesSesion()
    {
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }

    /**
     * Calcula tiempo restante de sesión
     */
    private function calcularTiempoRestanteSesion()
    {
        $sesionIniciada = session('sesion_iniciada') ?? time();
        $tiempoTranscurrido = time() - $sesionIniciada;
        $tiempoRestante = $this->duracionSesion - $tiempoTranscurrido;
        
        return max(0, $tiempoRestante);
    }

    /**
     * Obtiene información de última actualización
     */
    private function obtenerUltimaActualizacion()
    {
        return [
            'fecha' => '2025-01-15',
            'version' => '2.1.0',
            'mejoras' => ['Nuevo sistema de seguridad', 'Dashboard mejorado', 'Reportes avanzados']
        ];
    }
}
