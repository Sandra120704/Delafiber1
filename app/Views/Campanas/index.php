<?= $header ?>
<style>
        .metric-card {
            transition: transform 0.2s;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .metric-card:hover {
            transform: translateY(-5px);
        }
        .metric-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .metric-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .metric-card.success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .chart-container {
            height: 300px;
            margin-bottom: 2rem;
        }
        .progress-thin {
            height: 6px;
        }
        .campaign-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .campaign-row:hover {
            background-color: #f8f9fa;
        }
        .priority-high { border-left: 4px solid #dc3545; }
        .priority-medium { border-left: 4px solid #ffc107; }
        .priority-low { border-left: 4px solid #28a745; }
    </style>
<link rel="stylesheet" href="<?= base_url('css/campanas.css') ?>"> 
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<div class="container-fluid mt-4 px-4">
        
        <!-- Header mejorado -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">📊 Gestión de Campañas</h2>
                <p class="text-muted mb-0">Dashboard completo de marketing</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" id="exportBtn">
                    <i class="bi bi-download"></i> Exportar
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
                    <i class="bi bi-plus-lg"></i> Nueva Campaña
                </button>
            </div>
        </div>

        <!-- Métricas mejoradas con gráficos mini -->
        <div class="row mb-4 g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card metric-card text-white shadow-lg h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-bullseye fs-3 me-2"></i>
                                <h6 class="mb-0">Total Campañas</h6>
                            </div>
                            <h2 class="mb-0">24</h2>
                            <small class="opacity-75">+3 este mes</small>
                        </div>
                        <canvas id="totalChart" width="50" height="30"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card metric-card success text-white shadow-lg h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-lightning-charge-fill fs-3 me-2"></i>
                                <h6 class="mb-0">Activas</h6>
                            </div>
                            <h2 class="mb-0">8</h2>
                            <small class="opacity-75">67% del total</small>
                        </div>
                        <canvas id="activeChart" width="50" height="30"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card metric-card warning text-white shadow-lg h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-cash-stack fs-3 me-2"></i>
                                <h6 class="mb-0">Presupuesto</h6>
                            </div>
                            <h2 class="mb-0">S/ 125K</h2>
                            <small class="opacity-75">85% utilizado</small>
                        </div>
                        <div class="text-end">
                            <div class="progress progress-thin mb-2" style="width: 60px;">
                                <div class="progress-bar bg-white" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card metric-card info text-white shadow-lg h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-people-fill fs-3 me-2"></i>
                                <h6 class="mb-0">ROI Promedio</h6>
                            </div>
                            <h2 class="mb-0">3.2x</h2>
                            <small class="opacity-75">+0.5x vs anterior</small>
                        </div>
                        <i class="bi bi-graph-up fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos analíticos -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📈 Rendimiento de Campañas</h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary active" data-period="7d">7D</button>
                            <button class="btn btn-outline-primary" data-period="30d">30D</button>
                            <button class="btn btn-outline-primary" data-period="90d">90D</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">🎯 Estados de Campañas</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros avanzados -->
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">🔍 Buscar</label>
                    <input type="text" class="form-control" id="searchFilter" placeholder="Nombre, descripción...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">📊 Estado</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Todos</option>
                        <option value="activa">🟢 Activa</option>
                        <option value="pausada">🟡 Pausada</option>
                        <option value="finalizada">🔴 Finalizada</option>
                        <option value="borrador">⚪ Borrador</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">💰 Presupuesto</label>
                    <select class="form-select" id="budgetFilter">
                        <option value="">Todos</option>
                        <option value="0-5000">< S/ 5K</option>
                        <option value="5000-15000">S/ 5K - 15K</option>
                        <option value="15000-50000">S/ 15K - 50K</option>
                        <option value="50000+">S/ 50K+</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">🚨 Prioridad</label>
                    <select class="form-select" id="priorityFilter">
                        <option value="">Todas</option>
                        <option value="alta">🔴 Alta</option>
                        <option value="media">🟡 Media</option>
                        <option value="baja">🟢 Baja</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">📅 Rango de fechas</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="startDate">
                        <input type="date" class="form-control" id="endDate">
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-primary btn-sm me-2" id="applyFilters">
                        <i class="bi bi-funnel"></i> Aplicar Filtros
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" id="clearFilters">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </button>
                    <span class="ms-3 text-muted" id="filterResults"></span>
                </div>
            </div>
        </div>

        <!-- Tabla mejorada -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📋 Lista de Campañas</h5>
                <div class="d-flex gap-2">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" title="Vista tabla">
                            <i class="bi bi-table"></i>
                        </button>
                        <button class="btn btn-outline-secondary" title="Vista cards">
                            <i class="bi bi-grid"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="campaignsTable" class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Campaña</th>
                                <th>Estado</th>
                                <th>Fechas</th>
                                <th>Presupuesto</th>
                                <th>ROI</th>
                                <th>Leads</th>
                                <th>Responsable</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="campaignsTableBody">
                            <!-- Datos dinámicos -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Detalle Mejorado -->
    <div class="modal fade" id="campaignDetailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-eye"></i> Detalle de Campaña
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">📊 Métricas de Rendimiento</h6>
                                    <canvas id="detailChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">ℹ️ Información General</h6>
                                    <div id="campaignInfo">
                                        <!-- Información dinámica -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Campaña -->
    <div class="modal fade" id="createCampaignModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle"></i> Nueva Campaña
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createCampaignForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prioridad *</label>
                                <select class="form-select" required>
                                    <option value="alta">🔴 Alta</option>
                                    <option value="media" selected>🟡 Media</option>
                                    <option value="baja">🟢 Baja</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha Inicio *</label>
                                <input type="date" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha Fin *</label>
                                <input type="date" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Presupuesto *</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" class="form-control" min="0" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="createCampaignForm" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Crear Campaña
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Datos de ejemplo
        const campaignsData = [
            {
                id: 1,
                name: "Black Friday 2024",
                description: "Campaña especial para ofertas de temporada alta",
                status: "activa",
                priority: "alta",
                startDate: "2024-11-15",
                endDate: "2024-11-30",
                budget: 25000,
                spent: 18500,
                roi: 4.2,
                leads: 340,
                responsible: "María González"
            },
            {
                id: 2,
                name: "Lanzamiento Producto X",
                description: "Introducción de nuevo producto al mercado",
                status: "pausada",
                priority: "media",
                startDate: "2024-10-01",
                endDate: "2024-12-31",
                budget: 15000,
                spent: 8200,
                roi: 2.8,
                leads: 180,
                responsible: "Carlos Ruiz"
            },
            {
                id: 3,
                name: "Email Marketing Q4",
                description: "Campaña de email marketing para el último trimestre",
                status: "activa",
                priority: "baja",
                startDate: "2024-10-01",
                endDate: "2024-12-31",
                budget: 5000,
                spent: 3200,
                roi: 3.5,
                leads: 95,
                responsible: "Ana López"
            }
        ];

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            initializeTable();
            setupEventListeners();
        });

        function initializeCharts() {
            // Gráfico de rendimiento
            const performanceCtx = document.getElementById('performanceChart').getContext('2d');
            new Chart(performanceCtx, {
                type: 'line',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    datasets: [
                        {
                            label: 'Leads',
                            data: [120, 190, 300, 500, 200, 300, 450, 280, 350, 400, 340, 380],
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'ROI',
                            data: [2.1, 2.8, 3.2, 4.1, 2.5, 3.0, 3.8, 2.9, 3.5, 3.9, 4.2, 3.7],
                            borderColor: '#f093fb',
                            backgroundColor: 'rgba(240, 147, 251, 0.1)',
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });

            // Gráfico de estados
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Activas', 'Pausadas', 'Finalizadas', 'Borradores'],
                    datasets: [{
                        data: [8, 3, 10, 3],
                        backgroundColor: [
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#6c757d'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        function initializeTable() {
            const tbody = document.getElementById('campaignsTableBody');
            tbody.innerHTML = '';

            campaignsData.forEach(campaign => {
                const row = createCampaignRow(campaign);
                tbody.appendChild(row);
            });

            // Inicializar DataTable
            $('#campaignsTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
                }
            });
        }

        function createCampaignRow(campaign) {
            const row = document.createElement('tr');
            row.className = `campaign-row priority-${campaign.priority}`;
            
            const statusBadge = getStatusBadge(campaign.status);
            const priorityIcon = getPriorityIcon(campaign.priority);
            const budgetProgress = (campaign.spent / campaign.budget * 100).toFixed(1);

            row.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-2">${priorityIcon}</div>
                        <div>
                            <div class="fw-bold">${campaign.name}</div>
                            <small class="text-muted">${campaign.description.substring(0, 50)}...</small>
                        </div>
                    </div>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <small>
                        <i class="bi bi-calendar-event"></i> ${formatDate(campaign.startDate)}<br>
                        <i class="bi bi-calendar-x"></i> ${formatDate(campaign.endDate)}
                    </small>
                </td>
                <td>
                    <div>S/ ${campaign.budget.toLocaleString()}</div>
                    <div class="progress progress-thin">
                        <div class="progress-bar bg-info" style="width: ${budgetProgress}%"></div>
                    </div>
                    <small class="text-muted">${budgetProgress}% gastado</small>
                </td>
                <td>
                    <span class="badge ${campaign.roi > 3 ? 'bg-success' : campaign.roi > 2 ? 'bg-warning' : 'bg-danger'}">
                        ${campaign.roi}x
                    </span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-fill text-primary me-1"></i>
                        ${campaign.leads}
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="width: 24px; height: 24px; font-size: 0.7rem;">
                            ${campaign.responsible.split(' ').map(n => n[0]).join('')}
                        </div>
                        <small>${campaign.responsible}</small>
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="viewCampaign(${campaign.id})" title="Ver detalle">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="editCampaign(${campaign.id})" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteCampaign(${campaign.id})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            `;

            return row;
        }

        function getStatusBadge(status) {
            const badges = {
                'activa': '<span class="badge bg-success status-badge">🟢 Activa</span>',
                'pausada': '<span class="badge bg-warning status-badge">🟡 Pausada</span>',
                'finalizada': '<span class="badge bg-danger status-badge">🔴 Finalizada</span>',
                'borrador': '<span class="badge bg-secondary status-badge">⚪ Borrador</span>'
            };
            return badges[status] || badges['borrador'];
        }

        function getPriorityIcon(priority) {
            const icons = {
                'alta': '🔴',
                'media': '🟡',
                'baja': '🟢'
            };
            return icons[priority] || '⚪';
        }

        function formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('es-PE', {
                day: '2-digit',
                month: '2-digit',
                year: '2-digit'
            });
        }

        function setupEventListeners() {
            // Filtros
            document.getElementById('applyFilters').addEventListener('click', applyFilters);
            document.getElementById('clearFilters').addEventListener('click', clearFilters);

            // Crear campaña
            document.getElementById('createCampaignForm').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Campaña creada exitosamente!');
                bootstrap.Modal.getInstance(document.getElementById('createCampaignModal')).hide();
            });

            // Export
            document.getElementById('exportBtn').addEventListener('click', function() {
                alert('Exportando datos...');
            });
        }

        function applyFilters() {
            const searchTerm = document.getElementById('searchFilter').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const budgetFilter = document.getElementById('budgetFilter').value;
            const priorityFilter = document.getElementById('priorityFilter').value;

            let filteredData = campaignsData;

            // Aplicar filtros
            if (searchTerm) {
                filteredData = filteredData.filter(c => 
                    c.name.toLowerCase().includes(searchTerm) || 
                    c.description.toLowerCase().includes(searchTerm)
                );
            }

            if (statusFilter) {
                filteredData = filteredData.filter(c => c.status === statusFilter);
            }

            if (priorityFilter) {
                filteredData = filteredData.filter(c => c.priority === priorityFilter);
            }

            // Actualizar tabla
            const tbody = document.getElementById('campaignsTableBody');
            tbody.innerHTML = '';
            filteredData.forEach(campaign => {
                tbody.appendChild(createCampaignRow(campaign));
            });

            // Mostrar resultados
            document.getElementById('filterResults').textContent = 
                `Mostrando ${filteredData.length} de ${campaignsData.length} campañas`;
        }

        function clearFilters() {
            document.getElementById('searchFilter').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('budgetFilter').value = '';
            document.getElementById('priorityFilter').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('filterResults').textContent = '';
            
            initializeTable();
        }

        function viewCampaign(id) {
            const campaign = campaignsData.find(c => c.id === id);
            if (campaign) {
                // Poblar modal con datos
                document.getElementById('campaignInfo').innerHTML = `
                    <div class="mb-2"><strong>Nombre:</strong> ${campaign.name}</div>
                    <div class="mb-2"><strong>Estado:</strong> ${getStatusBadge(campaign.status)}</div>
                    <div class="mb-2"><strong>Prioridad:</strong> ${getPriorityIcon(campaign.priority)} ${campaign.priority.toUpperCase()}</div>
                    <div class="mb-2"><strong>Fechas:</strong> ${formatDate(campaign.startDate)} - ${formatDate(campaign.endDate)}</div>
                    <div class="mb-2"><strong>Presupuesto:</strong> S/ ${campaign.budget.toLocaleString()}</div>
                    <div class="mb-2"><strong>Gastado:</strong> S/ ${campaign.spent.toLocaleString()} (${(campaign.spent/campaign.budget*100).toFixed(1)}%)</div>
                    <div class="mb-2"><strong>ROI:</strong> ${campaign.roi}x</div>
                    <div class="mb-2"><strong>Leads:</strong> ${campaign.leads}</div>
                    <div class="mb-2"><strong>Responsable:</strong> ${campaign.responsible}</div>
                `;
                
                // Crear gráfico de detalle
                const detailCtx = document.getElementById('detailChart').getContext('2d');
                new Chart(detailCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Facebook', 'Google Ads', 'Email', 'LinkedIn', 'Instagram'],
                        datasets: [{
                            label: 'Leads por Canal',
                            data: [85, 120, 45, 30, 60],
                            backgroundColor: [
                                '#1877f2',
                                '#4285f4',
                                '#ea4335',
                                '#0077b5',
                                '#e4405f'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
                
                bootstrap.Modal.getOrCreateInstance(document.getElementById('campaignDetailModal')).show();
            }
        }

        function editCampaign(id) {
            alert(`Editando campaña ${id}`);
        }

        function deleteCampaign(id) {
            if (confirm('¿Está seguro de que desea eliminar esta campaña?')) {
                alert(`Campaña ${id} eliminada`);
                // Aquí iría la lógica para eliminar
            }
        }

        // Función para cambiar período en gráficos
        document.querySelectorAll('[data-period]').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Aquí actualizarías los datos del gráfico según el período
            });
        });
<?= $footer ?>
