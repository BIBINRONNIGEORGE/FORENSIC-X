/**
 * FORENSIC-X Dashboard Charts Initialization
 */

function initDashboardCharts(severityData, statusData) {
    if (typeof Chart === 'undefined') return;

    // 1. Severity Distribution Chart
    const sevCanvas = document.getElementById('severityChart');
    if (sevCanvas) {
        new Chart(sevCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Critical', 'High', 'Medium', 'Low'],
                datasets: [{
                    data: [
                        severityData.Critical || 0,
                        severityData.High || 0,
                        severityData.Medium || 0,
                        severityData.Low || 0
                    ],
                    backgroundColor: ['#EF4444', '#F97316', '#F59E0B', '#3B82F6'],
                    borderColor: '#0F172A',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94A3B8', font: { size: 11 } }
                    }
                },
                cutout: '65%'
            }
        });
    }

    // 2. Status Breakdown Bar Chart
    const statusCanvas = document.getElementById('statusChart');
    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'bar',
            data: {
                labels: ['Open', 'Investigating', 'Contained', 'Resolved', 'Closed'],
                datasets: [{
                    label: 'Incidents',
                    data: [
                        statusData.Open || 0,
                        statusData.Investigating || 0,
                        statusData.Contained || 0,
                        statusData.Resolved || 0,
                        statusData.Closed || 0
                    ],
                    backgroundColor: ['#EF4444', '#06B6D4', '#F59E0B', '#10B981', '#64748B'],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: { color: '#94A3B8' },
                        grid: { display: false }
                    },
                    y: {
                        ticks: { color: '#94A3B8', stepSize: 1 },
                        grid: { color: '#1E293B' }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
}
