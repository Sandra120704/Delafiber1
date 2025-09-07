<?php
namespace App\Controllers;
use App\Models\PersonaModel;
use App\Models\LeadModel;
use App\Models\SeguimientoModel;
use App\Models\TareaModel;

class LeadController extends BaseController
{
    public function crearPersona($personaData)
    {
        $model = new PersonaModel();
        return $model->insert($personaData); // retorna ID de la persona creada
    }

    // 2️⃣ Crear lead
    public function crearLead($leadData)
    {
        $model = new LeadModel();
        return $model->insert($leadData); // retorna ID del lead creado
    }

    // 3️⃣ Registrar seguimiento
    public function registrarSeguimiento($seguimientoData)
    {
        $model = new SeguimientoModel();
        return $model->insert($seguimientoData);
    }

    // 4️⃣ Crear tarea
    public function crearTarea($tareaData)
    {
        $model = new TareaModel();
        return $model->insert($tareaData);
    }

    // 🔹 Método completo para crear todo el flujo de un lead
    public function crearLeadCompleto()
    {
        // 1️⃣ Crear persona
        $personaData = [
            'nombres' => 'Luis',
            'apellidos' => 'Martinez',
            'dni' => '55667788',
            'correo' => 'luis.martinez@gmail.com',
            'telefono' => '999555666',
            'direccion' => 'Av. Ejemplo 123',
            'iddistrito' => 1
        ];
        $personaID = $this->crearPersona($personaData);

        // 2️⃣ Crear lead
        $leadData = [
            'idpersona' => $personaID,
            'idcampania' => 1,
            'idmedio' => 1,
            'idetapa' => 1,
            'estado' => 'nuevo'
        ];
        $leadID = $this->crearLead($leadData);

        // 3️⃣ Registrar seguimiento
        $seguimientoData = [
            'idlead' => $leadID,
            'idusuario' => 2,
            'idmodalidad' => 1,
            'comentario' => 'Llamada realizada, interesado'
        ];
        $this->registrarSeguimiento($seguimientoData);

        // 4️⃣ Crear tarea
        $tareaData = [
            'idusuario' => 2,
            'idlead' => $leadID,
            'descripcion' => 'Seguir contacto con Luis Martinez',
            'fecha_programada' => '2025-09-05 10:00:00',
            'estado' => 'pendiente'
        ];
        $this->crearTarea($tareaData);

        return "Flujo completo de lead creado correctamente.";
    }
    public function pruebaLeadCompleto()
{
    // 1️⃣ Crear persona
    $personaData = [
        'nombres' => 'Luis',
        'apellidos' => 'Martinez',
        'dni' => '55667788',
        'correo' => 'luis.martinez@gmail.com',
        'telefono' => '999555666',
        'direccion' => 'Av. Ejemplo 123',
        'iddistrito' => 1
    ];
    $personaID = $this->crearPersona($personaData);

    // 2️⃣ Crear lead
    $leadData = [
        'idpersona' => $personaID,
        'idcampania' => 1,
        'idmedio' => 1,
        'idetapa' => 1,
        'estado' => 'nuevo'
    ];
    $leadID = $this->crearLead($leadData);

    // 3️⃣ Registrar seguimiento
    $seguimientoData = [
        'idlead' => $leadID,
        'idusuario' => 2,
        'idmodalidad' => 1,
        'comentario' => 'Llamada realizada, interesado'
    ];
    $this->registrarSeguimiento($seguimientoData);

    // 4️⃣ Crear tarea
    $tareaData = [
        'idusuario' => 2,
        'idlead' => $leadID,
        'descripcion' => 'Seguir contacto con Luis Martinez',
        'fecha_programada' => '2025-09-05 10:00:00',
        'estado' => 'pendiente'
    ];
    $this->crearTarea($tareaData);

    // 5️⃣ Mostrar información completa en pantalla
    echo "✅ Lead completo creado:<br>";
    echo "Persona ID: $personaID <br>";
    echo "Lead ID: $leadID <br>";
    echo "<pre>";
    print_r($personaData);
    print_r($leadData);
    print_r($seguimientoData);
    print_r($tareaData);
    echo "</pre>";
}

}
