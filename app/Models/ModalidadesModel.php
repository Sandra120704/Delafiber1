<?php

namespace App\Models;

use CodeIgniter\Model;

class ModalidadesModel extends Model
{
    protected $table      = 'modalidades';   
    protected $primaryKey = 'idmodalidad';     

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';     
    protected $useTimestamps     = false;     

    protected $allowedFields = ['nombre'];     
}
