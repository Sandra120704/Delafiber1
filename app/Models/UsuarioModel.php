<?php

namespace App\Models;
use CodeIgniter\Model;

class UsuarioModel extends Model{
  protected $table = 'usuarios';

  protected $primarykey = 'idusuario';
  protected $allowedFields = ['idpersona','username','password','idrol','activo'];

  public function getByUsername($username){
    return $this->where('username',$username)->first();
  }
}