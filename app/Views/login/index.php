<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow-lg">
          <div class="card-header bg-primary text-white">Iniciar Sesión</div>
          <div class="card-body">
            <?php if(session()->getFlashdata('error')): ?>
              <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <form method="post" action="<?= base_url('login/auth') ?>">
              <div class="mb-3">
                <label>Usuario</label>
                <input type="text" name="usuario" class="form-control" required>
              </div>
              <div class="mb-3">
                <label>Contraseña</label>
                <input type="password" name="clave" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
