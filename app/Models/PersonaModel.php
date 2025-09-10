<?php
namespace App\Models;

use CodeIgniter\Model;

class PersonaModel extends Model
{
    protected $table = 'personas';
    protected $primaryKey = 'idpersona';
    protected $allowedFields = [
        'nombres','apellidos','dni','correo','telefono','direccion','referencias','iddistrito'
    ];
    protected function buscar($keyword){
        return $this->like('nombres', $keyword)
                    ->orLike('apellidos',$keyword)
                    ->orLike('dni',$keyword)
                    ->orLike('telefono',$keyword)
                    ->orLike('correo',$keyword)
                    ->findAll();
    }


    // Validaciones
    protected $validationRules = [
        'dni'        => 'required|exact_length[8]|is_unique[personas.dni]',
        'nombres'    => 'required|min_length[2]',
        'apellidos'  => 'required|min_length[2]',
        'telefono'   => 'required|numeric|min_length[9]|max_length[9]',
        'correo'     => 'permit_empty|valid_email',
        'direccion'  => 'permit_empty',
        'iddistrito' => 'required|is_natural_no_zero',
    ];

    protected $validationMessages = [
        'dni' => [
            'required' => 'El DNI es obligatorio.',
            'exact_length' => 'El DNI debe tener exactamente 8 dígitos.',
            'is_unique' => 'Ya existe una persona registrada con este DNI.'
        ],
        'nombres' => [
            'required' => 'Los nombres son obligatorios.',
            'min_length' => 'Debe ingresar al menos 2 caracteres.'
        ],
        'apellidos' => [
            'required' => 'Los apellidos son obligatorios.',
            'min_length' => 'Debe ingresar al menos 2 caracteres.'
        ],
        'telefono' => [
            'required' => 'El número de teléfono es obligatorio.',
            'numeric' => 'Solo se permiten números.',
            'min_length' => 'El número debe tener 9 dígitos.',
            'max_length' => 'El número debe tener 9 dígitos.'
        ],
        'correo' => [
            'valid_email' => 'Debe ingresar un correo electrónico válido.'
        ],
        'iddistrito' => [
            'required' => 'Debe seleccionar un distrito.',
            'is_natural_no_zero' => 'Debe seleccionar un distrito válido.'
        ]
    ];
}
