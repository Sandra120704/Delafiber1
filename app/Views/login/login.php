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
          <div class="card-header bg-primary text-white text-center">Iniciar Sesión</div>
          <div class="card-body">
            <?php if(session()->getFlashdata('error')): ?>
              <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <form action="<?= site_url('login/auth') ?>" method="post">
              <div class="mb-3">
                <input type="text" name="usuario" class="form-control" placeholder="Usuario" required>
              </div>
              <div class="mb-3">
                <input type="password" name="clave" class="form-control" placeholder="Contraseña" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
