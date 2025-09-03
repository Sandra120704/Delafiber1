<?php

namespace App\Controllers;
use App\Models\CampanaModel;
use Config\Database;
use App\Models\MedioModel;


class CampanaController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }
    public function index()
    {
        $model = new CampanaModel();
        $campanas = $model->orderBy('creado', 'DESC')->findAll();

        // Convertir cada elemento en objeto
        $campanas = array_map(fn($c) => (object) $c, $campanas);

        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');
        $data['campanas'] = $campanas;

        return view('Campanas/index', $data);
    }
    public function form($idcampania = null)
    {
        $campanaModel = new CampanaModel();
        $medioModel   = new MedioModel(); // modelo de medios
        $data = [];

        // Obtener todos los medios y convertirlos a objetos
        $medios = $medioModel->orderBy('medio')->findAll();
        $data['medios'] = array_map(fn($m) => (object) $m, $medios);

        if ($idcampania) {
            // Cargar campaña existente
            $campania = $campanaModel->find($idcampania);
            if (is_array($campania)) $campania = (object) $campania;
            $data['campania'] = $campania;

            // Obtener medios asociados a esta campaña
            $difusiones = db_connect()->table('difusiones')
                ->select('idmedio')
                ->where('idcampania', $idcampania)
                ->get()
                ->getResult();

            // Lista de IDs de medios asociados
            $data['difusiones_asociadas'] = array_map(fn($d) => $d->idmedio, $difusiones);
        } else {
            $data['difusiones_asociadas'] = []; // nueva campaña, ningún medio seleccionado
        }

        return view('Campanas/crear', $data);
    }
    public function guardar()
    {
        $campanaModel = new CampanaModel();
        $data = $this->request->getPost();

        // Capturar medios seleccionados
        $medios = $data['medios'] ?? [];
        unset($data['medios']); // no se guarda en la tabla campanias

        if (!empty($data['idcampania'])) {
            // Editar campaña existente
            $idcampania = $data['idcampania'];
            $data['modificado'] = date('Y-m-d H:i:s');
            $success = $campanaModel->update($idcampania, $data);
        } else {
            // Crear nueva campaña
            $data['creado'] = date('Y-m-d H:i:s');
            $idcampania = $campanaModel->insert($data);
            $success = $idcampania ? true : false;
        }

        if ($success) {
            $db = db_connect();

            // Limpiar medios previos (si es edición)
            $db->table('difusiones')->where('idcampania', $idcampania)->delete();

            // Guardar medios seleccionados
            foreach ($medios as $idmedio) {
                $db->table('difusiones')->insert([
                    'idcampania' => $idcampania,
                    'idmedio'    => $idmedio,
                    'creado'     => date('Y-m-d H:i:s')
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => $success,
            'mensaje' => $success ? 'Campaña guardada correctamente' : 'Error al guardar'
        ]);
    }

    public function eliminar()
    {
        $idcampania = $this->request->getPost('idcampania');
        $model = new CampanaModel();
        $success = $model->delete($idcampania);

        return $this->response->setJSON([
            'success' => $success ? true : false,
            'mensaje' => $success ? 'Campaña eliminada' : 'Error al eliminar'
        ]);
    }
    public function cambiarEstado()
    {
        $idcampania = $this->request->getPost('idcampania');
        $estado = $this->request->getPost('estado');

        $model = new CampanaModel();
        $success = $model->update($idcampania, ['estado' => $estado, 'modificado' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON([
            'success' => $success ? true : false,
            'mensaje' => $success ? 'Estado actualizado' : 'Error al actualizar'
        ]);
    }

}
