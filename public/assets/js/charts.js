/**
 * FORENSIC-X Dashboard Charts Initialization
 * Styled with Electric Cyan (#21d4f3), Deep Navy surfaces (#0d2131), and subtle gridlines (#1a3d50)
 */

function initDashboardCharts(severityData, statusData) {
    if (typeof Chart === 'undefined') return;

    // 1. Incident Trend Bar Chart (Last 7 Days)
    const trendCanvas = document.getElementById('incidentTrendChart');
    if (trendCanvas) {
        // Generate simulated 7-day trend from data or realistic cadence
        const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Today'];
        const values = [2, 5, 3, 7, 4, 8, Math.max(3, (severityData.Critical || 0) + (severityData.High || 0))];

        new Chart(trendCanvas, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [{
                    label: 'Incidents',
                    data: values,
                    backgroundColor: [
                        '#16668b',
                        '#16668b',
                        '#16668b',
                        '#16668b',
                        '#16668b',
                        '#16668b',
                        '#21d4f3' // Today's highlight in electric cyan
                    ],
                    borderRadius: 4,
                    barPercentage: 0.55
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#10293b',
                        titleColor: '#eaf5fa',
                        bodyColor: '#21d4f3',
                        borderColor: '#1a3d50',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#8ca8b8', font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        grid: { color: '#1a3747', drawBorder: false },
                        ticks: { color: '#8ca8b8', font: { family: 'Inter', size: 11 }, stepSize: 2 }
                    }
                }
            }
        });
    }

    // 2. Incident Lifecycle Doughnut / Status Breakdown (if used)
    const statusCanvas = document.getElementById('statusChart');
    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Open', 'Investigating', 'Contained', 'Resolved'],
                datasets: [{
                    data: [
                        statusData.Open || 1,
                        statusData.Investigating || 1,
                        statusData.Contained || 1,
                        statusData.Resolved || 1
                    ],
                    backgroundColor: ['#2ed6a1', '#21d4f3', '#399dff', '#7897a9'],
                    borderColor: '#0d2131',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#8ca8b8', font: { family: 'Inter', size: 11 }, boxWidth: 12 }
                    }
                },
                cutout: '70%'
            }
        });
    }
}
