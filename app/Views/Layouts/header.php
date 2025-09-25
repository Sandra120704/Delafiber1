<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Delafiber - CRM</title>

  <!-- Stylesheets -->
  <link rel="stylesheet" href="<?= base_url("assets/feather/feather.css") ?>">
  <link rel="stylesheet" href="<?= base_url('assets/ti-icons/css/themify-icons.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/datatables.net-bs4/dataTables.bootstrap4.css') ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?= base_url('css/vertical-layout-light/style.css') ?>">

  <link rel="shortcut icon" href="<?= base_url('image/favicon.ico') ?>" />
  
  <!-- Variables JavaScript globales -->
  <script>
    window.baseUrl = '<?= base_url() ?>';
    window.csrfToken = '<?= csrf_token() ?>';
    window.csrfHash = '<?= csrf_hash() ?>';
  </script>
  
  <!-- Meta tag para CSRF -->
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
</head>

<body>
  <div class="container-scroller">

    <!-- Navbar superior -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="<?= base_url() ?>">
          <img src="<?= base_url('images/logo-delafiber.png') ?>" class="mr-2" alt="logo"/>
        </a>
        <a class="navbar-brand brand-logo-mini" href="<?= base_url() ?>">
          <img src="<?= base_url('images/logo-mini.svg') ?>" alt="logo"/>
        </a>
      </div>

      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>

        <!-- Search -->
        <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Buscar por nombre, correo, teléfono o etapa" aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul>

        <!-- Botones y notificaciones -->
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              <span class="count"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notificaciones</p>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-success">
                    <i class="ti-info-alt mx-0"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">Application Error</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">Just now</p>
                </div>
              </a>
            </div>
          </li>

          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="<?= base_url('faces/face28.jpg') ?>" alt="profile"/>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item">
                <i class="ti-settings text-primary"></i> Configuración
              </a>
              <a class="dropdown-item" href="<?= site_url('logout') ?>">
                <i class="ti-power-off text-primary"></i> Cerrar sesión
              </a>
            </div>
          </li>
        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>

    <!-- Sidebar + Page content -->
    <div class="container-fluid page-body-wrapper">

      <!-- Sidebar lateral -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url(('dashboard/index') ) ?>">
              <i class='bx bx-grid-alt menu-icon'></i>
              <span class="menu-title">Inicio</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('leads') ?>">
              <i class='bx bx-sitemap menu-icon'></i>
              <span class="menu-title">Flujo de trabajo</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('oportunidades') ?>">
              <i class='bx bx-trending-up menu-icon'></i>
              <span class="menu-title">Oportunidades</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('tarea/tarea') ?>">
              <i class='bx bx-list-check menu-icon'></i>
              <span class="menu-title">Tareas</span>
            </a>
          </li>

          <li class="nav-item nav-divider">
            <p class="menu-title">Módulos</p>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('personas') ?>">
              <i class='bx bx-group menu-icon'></i>
              <span class="menu-title">Personas</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('usuarios') ?>">
              <i class='bx bx-user menu-icon'></i>
              <span class="menu-title">Usuarios</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('clientes') ?>">
              <i class='bx bx-briefcase-alt menu-icon'></i>
              <span class="menu-title">Clientes</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('mensajes') ?>">
              <i class='bx bx-message-dots menu-icon'></i>
              <span class="menu-title">Mensajes</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('campanas') ?>">
              <i class='bx bx-bullseye menu-icon'></i>
              <span class="menu-title">Campañas</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('reportes') ?>">
              <i class='bx bx-stats menu-icon'></i>
              <span class="menu-title">Reportes</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('configuracion') ?>">
              <i class='bx bx-cog menu-icon'></i>
              <span class="menu-title">Configuración</span>
            </a>
          </li>
        </ul>
      </nav>

      <!-- Contenido principal -->
      <div class="main-panel">
        <div class="content-wrapper">
          <!-- Aquí va el contenido de cada vista -->
