<!DOCTYPE html>
<html>
<head>
  <title>Login - CRM</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card p-4 shadow">
        <h4 class="mb-3 text-center">Login CRM</h4>
        <?php if(session('error')): ?>
          <div class="alert alert-danger"><?= session('error') ?></div>
        <?php endif; ?>
        <form method="post" action="<?= base_url('/doLogin') ?>">
          <div class="mb-3">
            <label>Usuario</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
