<?php

namespace App\Controllers;

use App\Models\CampanaModel;
use App\Models\MedioModel;
use Config\Database;

class CampanaController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Listado de campañas con su medio
    public function index()
    {
        $model = new CampanaModel();
        $campanas = $model->getCampanasConMedios(); // Asegúrate de que tu modelo haga join con difusiones + medio

        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');
        $data['campanas'] = $campanas;

        return view('Campanas/index', $data);
    }

    // Formulario crear / editar
    public function form($idcampania = null)
    {
        $campanaModel = new CampanaModel();
        $medioModel   = new MedioModel();
        $data = [];

        // Obtener todos los medios como objetos
        $medios = $medioModel->orderBy('medio')->findAll();
        $data['medios'] = array_map(fn($m) => (object) $m, $medios);

        if ($idcampania) {
            // Cargar campaña existente
            $campania = $campanaModel->find($idcampania);
            if (is_array($campania)) $campania = (object) $campania;
            $data['campania'] = $campania;

            // Medios asociados
            $difusiones = $this->db->table('difusiones')
                                    ->select('idmedio')
                                    ->where('idcampania', $idcampania)
                                    ->get()
                                    ->getResult();

            $data['difusiones_asociadas'] = array_map(fn($d) => $d->idmedio, $difusiones);
        } else {
            $data['difusiones_asociadas'] = [];
        }

        return view('Campanas/crear', $data);
    }

    // Guardar campaña (crear/editar)
    public function guardar()
    {
        $campanaModel = new CampanaModel();
        $db = db_connect();

        $data = $this->request->getPost();

        // Validar campos obligatorios
        if (empty($data['nombre']) || empty($data['fechainicio']) || empty($data['fechafin'])) {
            return $this->response->setJSON([
                'success' => false,
                'mensaje' => 'Por favor completa todos los campos obligatorios (Nombre, Fecha Inicio, Fecha Fin).'
            ]);
        }

        // Obtener solo el medio seleccionado (radio)
        $medio = $data['medio'] ?? null;
        unset($data['medio']); // no se inserta directamente en la tabla campanias

        $now = date('Y-m-d H:i:s');
        $success = false;

        try {
            if (!empty($data['idcampania'])) {
                // Actualizar campaña existente
                $idcampania = $data['idcampania'];
                $data['modificado'] = $now;
                unset($data['idcampania']);
                $success = $campanaModel->update($idcampania, $data);
            } else {
                // Crear nueva campaña
                $data['creado'] = $now;
                $idcampania = $campanaModel->insert($data);
                $success = $idcampania ? true : false;
            }

            // Guardar el medio en difusiones si todo va bien
            if ($success && $medio) {
                $tablaDifusion = 'difusiones'; // Cambia si tu tabla se llama 'difusion'
                
                // Borrar medios previos asociados
                $db->table($tablaDifusion)->where('idcampania', $idcampania)->delete();

                // Insertar nuevo medio
                $db->table($tablaDifusion)->insert([
                    'idcampania' => $idcampania,
                    'idmedio'    => $medio,
                    'creado'     => $now
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'mensaje' => 'Campaña guardada correctamente',
                'idcampania' => $idcampania
            ]);

        } catch (\Exception $e) {
            // Captura cualquier error de DB y envía el mensaje al JS
            return $this->response->setJSON([
                'success' => false,
                'mensaje' => 'Error al guardar la campaña: ' . $e->getMessage()
            ]);
        }
    }

    // Eliminar campaña
    public function eliminar()
    {
        $idcampania = $this->request->getPost('idcampania');
        $model = new CampanaModel();

        // Primero eliminamos los registros asociados en difusiones
        $this->db->table('difusiones')->where('idcampania', $idcampania)->delete();

        // Luego eliminamos la campaña
        $success = $model->delete($idcampania);

        return $this->response->setJSON([
            'success' => $success ? true : false,
            'mensaje' => $success ? 'Campaña eliminada' : 'Error al eliminar'
        ]);
    }

    // Cambiar estado (activo / inactivo)
    public function cambiarEstado()
    {
        $idcampania = $this->request->getPost('idcampania');
        $estado = $this->request->getPost('estado');

        $model = new CampanaModel();
        $success = $model->update($idcampania, [
            'estado' => $estado,
            'modificado' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => $success ? true : false,
            'mensaje' => $success ? 'Estado actualizado' : 'Error al actualizar'
        ]);
    }
}
