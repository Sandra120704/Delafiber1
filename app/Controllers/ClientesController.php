<?php

namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\LeadModel;
use App\Models\TareaModel;

/**
 * ===================================================
 * CONTROLADOR DE GESTIÓN DE CLIENTES - DELAFIBER
 * ===================================================
 * 
 * Este controlador maneja toda la gestión de clientes de fibra óptica:
 * - Lista de clientes activos e inactivos
 * - Servicios contratados (planes de internet, TV, telefonía)
 * - Historial de instalaciones y soporte técnico
 * - Estados de conexión y pagos
 * - Reportes de satisfacción al cliente
 * 
 * Empresa: Delafiber (Servicios de Fibra Óptica)
 * @author Tu Nombre
 * @date 2025
 */
class ClientesController extends BaseController
{
    // ===== MODELOS QUE UTILIZAREMOS =====
    protected $personaModel;      // Para datos básicos del cliente
    protected $leadModel;         // Para historial de prospección
    protected $tareaModel;        // Para seguimiento de servicios

    /**
     * Constructor - Inicializa todos los modelos necesarios
     */
    public function __construct()
    {
        $this->personaModel = new PersonaModel();
        $this->leadModel = new LeadModel();
        $this->tareaModel = new TareaModel();
    }

    /**
     * ===============================================
     * PÁGINA PRINCIPAL DE GESTIÓN DE CLIENTES
     * ===============================================
     * 
     * Muestra un dashboard completo con:
     * - Lista de clientes activos
     * - Estado de servicios (instalados, pendientes, suspendidos)
     * - Métricas importantes (satisfacción, ingresos, etc.)
     */
    public function index()
    {
        // Obtener estadísticas importantes para el dashboard
        $datosClientes = [
            // ===== INFORMACIÓN BÁSICA =====
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            
            // ===== ESTADÍSTICAS CLAVE =====
            'totalClientes' => $this->obtenerTotalClientes(),
            'clientesActivos' => $this->obtenerClientesActivos(),
            'nuevosEsteMes' => $this->obtenerNuevosClientesEsteMes(),
            'serviciosPendientes' => $this->obtenerServiciosPendientes(),
            
            // ===== DATOS PARA LA TABLA =====
            'clientes' => $this->obtenerListaClientesCompleta(),
            
            // ===== FILTROS DISPONIBLES =====
            'tiposServicio' => $this->obtenerTiposDeServicio(),
            'estadosConexion' => $this->obtenerEstadosConexion()
        ];

        return view('clientes/index', $datosClientes);
    }

    /**
     * ===============================================
     * VISTA DETALLADA DE UN CLIENTE ESPECÍFICO
     * ===============================================
     */
    public function detalle($idCliente)
    {
        // Buscar información completa del cliente
        $cliente = $this->personaModel->find($idCliente);
        
        if (!$cliente) {
            return redirect()->to('clientes')->with('error', 'Cliente no encontrado');
        }

        $datosDetalle = [
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            'cliente' => $cliente,
            'serviciosContratados' => $this->obtenerServiciosDelCliente($idCliente),
            'historialSoporte' => $this->obtenerHistorialSoporte($idCliente),
            'estadoCuenta' => $this->obtenerEstadoCuenta($idCliente)
        ];

        return view('clientes/detalle', $datosDetalle);
    }

    /**
     * ===============================================
     * MÉTODOS AUXILIARES PARA OBTENER DATOS
     * ===============================================
     */

    /**
     * Obtiene el total de clientes registrados
     */
    private function obtenerTotalClientes()
    {
        return $this->personaModel->countAll();
    }

    /**
     * Obtiene clientes con servicios activos
     */
    private function obtenerClientesActivos()
    {
        // TODO: Implementar lógica específica para clientes activos
        return $this->personaModel->where('estado', 'activo')->countAllResults();
    }

    /**
     * Obtiene nuevos clientes del mes actual
     */
    private function obtenerNuevosClientesEsteMes()
    {
        $inicioMes = date('Y-m-01');
        return $this->personaModel
            ->where('created_at >=', $inicioMes)
            ->countAllResults();
    }

    /**
     * Obtiene servicios pendientes de instalación
     */
    private function obtenerServiciosPendientes() 
    {
        return $this->tareaModel
            ->where('estado', 'pendiente')
            ->like('descripcion', 'instalación', 'both')
            ->countAllResults();
    }

    /**
     * Obtiene lista completa de clientes con información relevante
     */
    private function obtenerListaClientesCompleta()
    {
        return $this->personaModel
            ->select('personas.*, 
                     COUNT(tareas.idtarea) as servicios_pendientes,
                     MAX(tareas.fecha_vencimiento) as ultimo_servicio')
            ->join('tareas', 'tareas.idpersona = personas.idpersona', 'left')
            ->groupBy('personas.idpersona')
            ->orderBy('personas.nombres', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene tipos de servicio disponibles
     */
    private function obtenerTiposDeServicio()
    {
        return [
            'fibra_hogar' => 'Fibra Óptica Hogar',
            'fibra_empresa' => 'Fibra Óptica Empresarial',
            'internet_tv' => 'Internet + TV',
            'telefonia' => 'Telefonía IP',
            'soporte_tecnico' => 'Soporte Técnico'
        ];
    }

    /**
     * Obtiene estados de conexión posibles
     */
    private function obtenerEstadosConexion()
    {
        return [
            'activo' => 'Activo',
            'suspendido' => 'Suspendido',
            'instalacion' => 'En Instalación',
            'mantenimiento' => 'En Mantenimiento',
            'cancelado' => 'Cancelado'
        ];
    }

    /**
     * Obtiene servicios contratados de un cliente específico
     */
    private function obtenerServiciosDelCliente($idCliente)
    {
        return $this->tareaModel
            ->where('idpersona', $idCliente)
            ->orderBy('fecha_creacion', 'DESC')
            ->findAll();
    }

    /**
     * Obtiene historial de soporte técnico
     */
    private function obtenerHistorialSoporte($idCliente)
    {
        return $this->tareaModel
            ->where('idpersona', $idCliente)
            ->like('tipo', 'soporte', 'both')
            ->orderBy('fecha_creacion', 'DESC')
            ->findAll();
    }

    /**
     * Obtiene estado de cuenta del cliente
     */
    private function obtenerEstadoCuenta($idCliente)
    {
        // TODO: Implementar lógica de facturación y pagos
        return [
            'saldo_pendiente' => 0,
            'ultimo_pago' => date('Y-m-d'),
            'proximo_vencimiento' => date('Y-m-d', strtotime('+30 days')),
            'estado_cuenta' => 'Al día'
        ];
    }
}