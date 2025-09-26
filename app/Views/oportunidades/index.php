<?= $header ?>

<div class="container mt-4">
    <h2>Oportunidades de Negocio</h2>
    <div class="alert alert-info">
        Una oportunidad representa un posible negocio, venta o cliente interesado en los servicios de la empresa. Aquí puedes ver y gestionar todas las oportunidades comerciales: cotizaciones en proceso, clientes potenciales y ventas en seguimiento.
    </div>
    <a href="<?= site_url('oportunidades/crear') ?>" class="btn btn-primary mb-3">Nueva Oportunidad</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Valor Estimado</th>
                <th>Fecha de Cierre</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Venta Fibra Óptica a Empresa X</td>
                <td>Instalación de fibra óptica dedicada para empresa X.</td>
                <td>S/ 8,500.00</td>
                <td>2025-10-15</td>
                <td>En proceso</td>
                <td>
                    <a href="#" class="btn btn-sm btn-info">Ver</a>
                    <a href="#" class="btn btn-sm btn-warning">Editar</a>
                    <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Upgrade servicio residencial</td>
                <td>Mejora de plan de fibra para cliente residencial.</td>
                <td>S/ 350.00</td>
                <td>2025-09-30</td>
                <td>Cerrado</td>
                <td>
                    <a href="#" class="btn btn-sm btn-info">Ver</a>
                    <a href="#" class="btn btn-sm btn-warning">Editar</a>
                    <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>Instalación fibra zona industrial</td>
                <td>Proyecto de conectividad para parque industrial.</td>
                <td>S/ 15,000.00</td>
                <td>2025-11-05</td>
                <td>Nuevo</td>
                <td>
                    <a href="#" class="btn btn-sm btn-info">Ver</a>
                    <a href="#" class="btn btn-sm btn-warning">Editar</a>
                    <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?= $footer ?>