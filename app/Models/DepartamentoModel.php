<?php
namespace App\Models;
use CodeIgniter\Model;

class DepartamentoModel extends Model
{
    protected $table = 'departamentos';
    protected $primaryKey = 'idDepartamento';
    protected $allowedFields = ['departamento'];
}
