<?php

namespace App\Models;

use CodeIgniter\Model;

class Origen extends Model
{
    protected $table      = 'origenes';      
    protected $primaryKey = 'idorigen';
    
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';     
    protected $useSoftDeletes = false;

    protected $allowedFields = ['nombre'];   

    protected $useTimestamps = false;     
}
