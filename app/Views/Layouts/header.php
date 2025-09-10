<!DOCTYPE html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Delafiber - CRM</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?= base_url("assets/feather/feather.css")?>">
  <link rel="stylesheet" href="<?= base_url('assets/ti-icons/css/themify-icons.css')?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vendor.bundle.base.css')?>">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="<?= base_url('assets/datatables.net-bs4/dataTables.bootstrap4.css')?>">
  <link rel="stylesheet" href="<?= base_url('assets/ti-icons/css/themify-icons.css')?>">
  <link rel="stylesheet" type="<?= base_url('text/css" href="js/select.dataTables.min.css')?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?= base_url('css/vertical-layout-light/style.css')?>">
  <!-- endinject -->
  <link rel="shortcut icon" href="<?= base_url('images/favicon.png')?>" />
</head>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="<?= base_url() ?>"><img src="<?= base_url('images/logo-delafiber.png')?>" class="mr-2" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="<?= base_url() ?>"><img src="<?= base_url('images/logo-mini.svg')?>" alt="logo"/></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Buscar persona" aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              <span class="count"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-success">
                    <i class="ti-info-alt mx-0"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">Application Error</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    Just now
                  </p>
                </div>
              </a>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-warning">
                    <i class="ti-settings mx-0">
                      Cerrar Sesión
                    </i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">Settings</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    Private message
                  </p>
                </div>
              </a>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-info">
                    <i class="ti-user mx-0"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">New user registration</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    2 days ago
                  </p>
                </div>
              </a>
            </div>
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="<? base_url()?>" data-toggle="dropdown" id="profileDropdown">
              <img src="<?= base_url("/faces/face28.jpg")?>" alt="profile"/>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item">
                <i class="ti-settings text-primary"></i>
                Configuración
              </a>
              <a class="dropdown-item">
                <i class="ti-power-off text-primary"></i>
                Cerrar sesión
              </a>
            </div>
          </li>
          
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="ti-settings"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close ti-close"></i>
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options selected" id="sidebar-light-theme"><div class="img-ss rounded-circle bg-light border mr-3"></div>Light</div>
          <div class="sidebar-bg-options" id="sidebar-dark-theme"><div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark</div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles success"></div>
            <div class="tiles warning"></div>
            <div class="tiles danger"></div>
            <div class="tiles info"></div>
            <div class="tiles dark"></div>
            <div class="tiles default"></div>
          </div>
        </div>
      </div>
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <!-- Inicio -->
    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('inicio') ?>">
        <i class="icon-grid menu-icon"></i>
        <span class="menu-title">Inicio</span>
      </a>
    </li>

    <!-- Procesos -->
    <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#procesos" aria-expanded="false" aria-controls="procesos">
        <i class="icon-layout menu-icon"></i>
        <span class="menu-title">Procesos</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="procesos">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="<?= base_url('campanas') ?>">Campañas</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('leads') ?>">Flujo de trabajo</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('oportunidades') ?>">Oportunidades</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('tareas') ?>">Tareas</a></li>
        </ul>
      </div>
    </li>

    <!-- Módulos -->
    <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#modulos" aria-expanded="false" aria-controls="modulos">
        <i class="icon-columns menu-icon"></i>
        <span class="menu-title">Módulos</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="modulos">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="<?= base_url('personas') ?>">Personas</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('usuarios') ?>">Usuarios</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('clientes') ?>">Clientes</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('mensajes') ?>">Mensajes</a></li>
        </ul>
      </div>
    </li>

    <!-- Reportes -->
    <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#reportes" aria-expanded="false" aria-controls="reportes">
        <i class="icon-bar-graph menu-icon"></i>
        <span class="menu-title">Reportes</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="reportes">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('estadisticas') ?>">Estadísticas</a></li>
        </ul>
      </div>
    </li>

    <!-- Configuración -->
    <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#configuracion" aria-expanded="false" aria-controls="configuracion">
        <i class="icon-settings menu-icon"></i>
        <span class="menu-title">Configuración</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="configuracion">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="<?= base_url('roles') ?>">Roles & Permisos</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('integraciones') ?>">Integraciones</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('preferencias') ?>">Preferencias</a></li>
        </ul>
      </div>
    </li>

    <!-- Documentación -->
    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('documentacion') ?>">
        <i class="icon-paper menu-icon"></i>
        <span class="menu-title">Documentación</span>
      </a>
    </li>

    <!-- Cerrar sesión -->
    <li class="nav-item">
      <a class="nav-link" href="<?= site_url('logout') ?>">
        <i class="ti-power-off text-primary"></i>
        <span class="menu-title">Cerrar Sesión</span>
      </a>
    </li>
  </ul>
</nav>

      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">

        