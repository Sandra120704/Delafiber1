<?php
namespace App\Models;
use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table = 'leads';
    protected $primaryKey = 'idlead';
    protected $allowedFields = [
        'idpersona', 'idcampania', 'idmedio', 'idetapa', 'estado'
    ];
}
