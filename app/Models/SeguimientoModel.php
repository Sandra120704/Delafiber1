<?php
namespace App\Models;
use CodeIgniter\Model;

class SeguimientoModel extends Model
{
    protected $table = 'seguimientos';
    protected $primaryKey = 'idseguimiento';
    protected $allowedFields = [
        'idlead', 'idmodalidad', 'resultado_contacto', 'proxima_accion', 'fecha'
    ];

    // Obtener seguimientos de un lead ordenados por fecha descendente
    public function getByLead($idlead)
    {
        return $this->where('idlead', $idlead)
                    ->orderBy('fecha', 'DESC')
                    ->findAll();
    }
}
