<?php

namespace App\Models;
use CodeIgniter\Model;

class DifunsionModel extends Model {
    protected $table      = 'difusiones';
    protected $primaryKey = 'iddifusion';
    protected $allowedFields = ['idcampania','idmedio','creado','inversion','leads','leads_generado','creado','modificado'];
}
