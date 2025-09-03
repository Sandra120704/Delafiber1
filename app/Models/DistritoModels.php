<?php
namespace App\Models;
use CodeIgniter\Model;

class DistritoModels extends Model
{
    protected $table = 'distritos';
    protected $primaryKey = 'iddistrito';
    protected $allowedFields = ['distrito','idprovincia'];
}
