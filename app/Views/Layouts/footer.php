        </div>
        <!-- content-wrapper ends -->

        <!-- partial:partials/_footer.html -->
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                    Copyright © 2025 - Todos los derechos reservados
                </span>
                <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
                    Delafiber Perú <i class="ti-heart text-danger ml-1"></i>
                </span>
            </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>   
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="<?= base_url('assets/js/vendor.bundle.base.js')?>"></script>
  <!-- endinject -->

  <!-- Plugin js for this page -->
  <script src="<?= base_url('assets/chart.js/Chart.min.js')?>"></script>
  <script src="<?= base_url('assets/datatables.net/jquery.dataTables.js')?>"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?= base_url('js/dataTables.select.min.js')?>"></script>
  <!-- End plugin js for this page -->

  <!-- inject:js - ARCHIVOS PRINCIPALES DEL LAYOUT -->
  <script src="<?= base_url('js/off-canvas.js')?>"></script>
  <script src="<?= base_url('js/hoverable-collapse.js')?>"></script>
  <script src="<?= base_url('js/template.js')?>"></script>
  <script src="<?= base_url('js/settings.js')?>"></script>
  <!-- endinject -->

  <!-- ARCHIVOS ESPECÍFICOS POR MÓDULO -->
  <?php 
  $currentController = service('router')->controllerName();
  $currentMethod = service('router')->methodName();
  ?>
  
  <!-- Dashboard específico -->
  <?php if (strpos($currentController, 'Dashboard') !== false): ?>
    <script src="<?= base_url('js/dashboard.js')?>"></script>
    <script src="<?= base_url('js/Chart.roundedBarCharts.js')?>"></script>
    <script src="<?= base_url('js/todolist.js')?>"></script>
  <?php endif; ?>
  
  <!-- Tareas específicas -->
  <?php if (strpos($currentController, 'Tarea') !== false): ?>
    <script src="<?= base_url('js/tareaJS/tarea.js')?>"></script>
  <?php endif; ?>
  
  <!-- Personas específicas -->
  <?php if (strpos($currentController, 'Persona') !== false): ?>
    <script src="<?= base_url('js/personasJS/personas.js')?>"></script>
    <script src="<?= base_url('js/personasJS/index.js')?>"></script>
    <script src="<?= base_url('js/personasJS/alert.js')?>"></script>
    <script src="<?= base_url('js/leadsJS/modals.js')?>"></script>
  <?php endif; ?>
  
  <!-- Leads específicos -->
  <?php if (strpos($currentController, 'Lead') !== false): ?>
    <script src="<?= base_url('js/leadsJS/leads.js')?>"></script>
    <script src="<?= base_url('js/leadsJS/kanbas.js')?>"></script>
    <script src="<?= base_url('js/leadsJS/modals.js')?>"></script>
  <?php endif; ?>
  
  <!-- Campañas específicas -->
  <?php if (strpos($currentController, 'Campana') !== false): ?>
    <script src="<?= base_url('js/campanasJS/campana.js')?>"></script>
  <?php endif; ?>
  
  <!-- Usuarios específicos -->
  <?php if (strpos($currentController, 'Usuario') !== false): ?>
    <script src="<?= base_url('js/usuariosJS/usuario.js')?>"></script>
  <?php endif; ?>
  
  <!-- End custom js for this page-->
</body>

</html>
