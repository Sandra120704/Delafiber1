<?= $header ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<div class="content-wrapper">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Gestión de Usuarios</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard/index') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
            <i class="bx bx-plus"></i> Nuevo Usuario
        </button>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4><?= count($usuarios ?? []) ?></h4>
                    <small>Total Usuarios</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4><?= count(array_filter($usuarios ?? [], fn($u) => $u['activo'] ?? true)) ?></h4>
                    <small>Activos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4><?= count(array_filter($usuarios ?? [], fn($u) => ($u['nombre_rol'] ?? '') === 'vendedor')) ?></h4>
                    <small>Vendedores</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4><?= count(array_filter($usuarios ?? [], fn($u) => ($u['nombre_rol'] ?? '') === 'admin')) ?></h4>
                    <small>Administradores</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary active" onclick="filtrarUsuarios('todos')">Todos</button>
                        <button class="btn btn-outline-success" onclick="filtrarUsuarios('activos')">Activos</button>
                        <button class="btn btn-outline-danger" onclick="filtrarUsuarios('inactivos')">Inactivos</button>
                        <button class="btn btn-outline-warning" onclick="filtrarUsuarios('vendedores')">Vendedores</button>
                        <button class="btn btn-outline-info" onclick="filtrarUsuarios('admins')">Admins</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar usuarios..." id="buscarUsuario">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Lista de Usuarios</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($usuarios)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Información Personal</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Estadísticas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $colors = ['#8e44ad','#2980b9','#16a085','#e67e22','#c0392b'];
                            foreach ($usuarios as $usuario): 
                                $color = $colors[$usuario['idusuario'] % count($colors)];
                                $activo = $usuario['activo'] ?? true;
                            ?>
                            <tr data-rol="<?= strtolower($usuario['nombre_rol'] ?? '') ?>" data-estado="<?= $activo ? 'activo' : 'inactivo' ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="user-avatar" style="background:<?= $color ?>; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:bold;">
                                                <?= strtoupper(substr($usuario['username'] ?? 'U', 0, 2)) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= esc($usuario['username'] ?? 'Sin usuario') ?></div>
                                            <small class="text-muted">ID: <?= $usuario['idusuario'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= esc($usuario['nombre_persona'] ?? 'Sin asignar') ?></div>
                                    <small class="text-muted">
                                        <?php if (!empty($usuario['email'])): ?>
                                            <i class="bx bx-envelope"></i> <?= esc($usuario['email']) ?><br>
                                        <?php endif; ?>
                                        <?php if (!empty($usuario['telefono'])): ?>
                                            <i class="bx bx-phone"></i> <?= esc($usuario['telefono']) ?>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        ($usuario['nombre_rol'] ?? '') === 'admin' ? 'danger' : 
                                        (($usuario['nombre_rol'] ?? '') === 'supervisor' ? 'warning' : 'primary') 
                                    ?>">
                                        <?= esc($usuario['nombre_rol'] ?? 'Sin rol') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input estado-switch" type="checkbox" 
                                               <?= $activo ? 'checked' : '' ?> 
                                               data-id="<?= $usuario['idusuario'] ?>">
                                        <label class="form-check-label">
                                            <span class="badge bg-<?= $activo ? 'success' : 'secondary' ?>">
                                                <?= $activo ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <div>Leads: <strong><?= $usuario['total_leads'] ?? 0 ?></strong></div>
                                        <div>Tareas: <strong><?= $usuario['total_tareas'] ?? 0 ?></strong></div>
                                        <div>Conversión: <strong><?= $usuario['conversion_rate'] ?? '0' ?>%</strong></div>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-editar" data-id="<?= $usuario['idusuario'] ?>" title="Editar">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info btn-ver-perfil" data-id="<?= $usuario['idusuario'] ?>" title="Ver perfil">
                                            <i class="bx bx-user"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-resetear-password" data-id="<?= $usuario['idusuario'] ?>" title="Resetear contraseña">
                                            <i class="bx bx-key"></i>
                                        </button>
                                        <?php if (($usuario['nombre_rol'] ?? '') !== 'admin'): ?>
                                        <button class="btn btn-outline-danger btn-eliminar" data-id="<?= $usuario['idusuario'] ?>" title="Eliminar">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bx bx-user display-1 text-muted"></i>
                    <h5 class="text-muted">No hay usuarios registrados</h5>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                        <i class="bx bx-plus"></i> Crear primer usuario
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para crear/editar usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formUsuario">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idusuario" name="idusuario">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Usuario/Username</label>
                                <input type="text" class="form-control" name="usuario" required>
                                <small class="text-muted">Será usado para iniciar sesión</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="clave">
                                <small class="text-muted">Déjalo vacío para mantener la actual (solo edición)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Persona Asociada</label>
                                <select class="form-select" name="idpersona">
                                    <option value="">Seleccionar persona</option>
                                    <?php if (!empty($personas)): ?>
                                        <?php foreach ($personas as $persona): ?>
                                            <option value="<?= $persona['idpersona'] ?>">
                                                <?= esc($persona['nombres'] . ' ' . $persona['apellidos']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <select class="form-select" name="idrol" required>
                                    <option value="">Seleccionar rol</option>
                                    <?php if (!empty($roles)): ?>
                                        <?php foreach ($roles as $rol): ?>
                                            <option value="<?= $rol['idrol'] ?>">
                                                <?= esc($rol['nombre']) ?> - <?= esc($rol['descripcion']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" id="activoSwitch" checked>
                            <label class="form-check-label" for="activoSwitch">
                                Usuario activo
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de perfil de usuario -->
<div class="modal fade" id="modalPerfilUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Perfil de Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoPerfilUsuario">
                <!-- Se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const baseUrl = '<?= base_url() ?>';

$(document).ready(function() {
    // Cambiar estado activo/inactivo
    $('.estado-switch').change(function() {
        const usuarioId = $(this).data('id');
        const activo = $(this).is(':checked');
        
        $.post(`${baseUrl}usuarios/cambiarEstado/${usuarioId}`, {
            activo: activo ? 1 : 0
        })
        .done(function(response) {
            if (response.success) {
                const badge = $(this).closest('td').find('.badge');
                badge.removeClass('bg-success bg-secondary')
                     .addClass(activo ? 'bg-success' : 'bg-secondary')
                     .text(activo ? 'Activo' : 'Inactivo');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        }.bind(this));
    });

    // Crear/Editar usuario
    $('#formUsuario').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const usuarioId = $('#idusuario').val();
        const url = usuarioId ? `${baseUrl}usuarios/editar/${usuarioId}` : `${baseUrl}usuarios/crear`;
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success) {
                $('#modalUsuario').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 2000);
            } else {
                Swal.fire('Error', response.message || 'Error al guardar usuario', 'error');
            }
        })
        .fail(function(xhr) {
            console.log('Error:', xhr.responseText);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
    });

    // Eliminar usuario
    $(document).on('click', '.btn-eliminar', function() {
        const usuarioId = $(this).data('id');
        
        Swal.fire({
            title: '¿Eliminar usuario?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}usuarios/eliminar/${usuarioId}`,
                    method: 'DELETE',
                    dataType: 'json'
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Eliminado', 'Usuario eliminado correctamente', 'success');
                        setTimeout(() => location.reload(), 2000);
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'No se pudo eliminar el usuario', 'error');
                });
            }
        });
    });

    // Buscar usuarios
    $('#buscarUsuario').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });

    // Resetear contraseña
    $(document).on('click', '.btn-resetear-password', function() {
        const usuarioId = $(this).data('id');
        
        Swal.fire({
            title: 'Resetear contraseña',
            input: 'password',
            inputLabel: 'Nueva contraseña',
            inputPlaceholder: 'Ingresa la nueva contraseña',
            showCancelButton: true,
            confirmButtonText: 'Cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.post(`${baseUrl}usuarios/resetearPassword/${usuarioId}`, {
                    nueva_password: result.value
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', 'Contraseña actualizada', 'success');
                    }
                });
            }
        });
    });
});

// Filtrar usuarios
function filtrarUsuarios(filtro) {
    // Actualizar botones activos
    $('.btn-group button').removeClass('active');
    event.target.classList.add('active');
    
    $('tbody tr').show();
    
    switch(filtro) {
        case 'activos':
            $('tbody tr[data-estado="inactivo"]').hide();
            break;
        case 'inactivos':
            $('tbody tr[data-estado="activo"]').hide();
            break;
        case 'vendedores':
            $('tbody tr:not([data-rol="vendedor"])').hide();
            break;
        case 'admins':
            $('tbody tr:not([data-rol="admin"])').hide();
            break;
    }
}
</script>

<?= $footer ?>