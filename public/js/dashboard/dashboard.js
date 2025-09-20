document.addEventListener('DOMContentLoaded', function() {
    // Datos del Pipeline
    const pipelineData = <?= json_encode($pipeline_data ?? []) ?>;
    
    // Gráfico de Pipeline
    const pipelineCtx = document.getElementById('pipelineChart').getContext('2d');
    new Chart(pipelineCtx, {
        type: 'doughnut',
        data: {
            labels: pipelineData.map(item => item.nombre),
            datasets: [{
                data: pipelineData.map(item => item.total),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Datos de Campañas
    const campanasData = <?= json_encode($campanas_data ?? []) ?>;
    
    // Gráfico de Campañas
    if (campanasData.length > 0) {
        const campanasCtx = document.getElementById('campanasChart').getContext('2d');
        new Chart(campanasCtx, {
            type: 'bar',
            data: {
                labels: campanasData.map(item => item.nombre),
                datasets: [{
                    label: 'Leads Generados',
                    data: campanasData.map(item => item.total_leads),
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});