import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('loyaltyDistributionChart');
    if (!canvas) return;

    // 1. Extract the dynamic datasets passed down from your Laravel Service
    const chartLabels = JSON.parse(canvas.getAttribute('data-labels')) || [];
    const chartValues = JSON.parse(canvas.getAttribute('data-values')) || [];

    // 2. Initialize the horizontal bar graph instance
    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Staff Members Count',
                data: chartValues,
                backgroundColor: [
                    '#a855f7', // Purple for All 6
                    '#10b981', // Green for 4-5
                    '#3b82f6', // Blue for 2-3
                    '#f59e0b', // Yellow for 1
                    '#ef4444'  // Red for 0
                ],
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y', // Flipped axis makes it a horizontal bar chart
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false } // Hides the top legend since bars are labeled
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0, font: { family: "'Lato', 'Segoe UI', sans-serif" } },
                    grid: { display: false }
                },
                y: {
                    ticks: { font: { family: "'Lato', 'Segoe UI', sans-serif", weight: 'bold' } },
                    grid: { display: false }
                }
            }
        }
    });
});
