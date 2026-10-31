document.addEventListener("DOMContentLoaded", function() {
    const chartCanvas = document.getElementById('categoryChart');
    if (!chartCanvas) return;

    // 1. Gather all 8 Registration datasets
    const registeredData = [
        parseInt(chartCanvas.dataset.regPhysical) || 0,
        parseInt(chartCanvas.dataset.regMental) || 0,
        parseInt(chartCanvas.dataset.regFinancial) || 0,
        parseInt(chartCanvas.dataset.regSocial) || 0,
        parseInt(chartCanvas.dataset.regIntellectual) || 0,
        parseInt(chartCanvas.dataset.regOccupational) || 0,
        parseInt(chartCanvas.dataset.regEnvironmental) || 0,
        parseInt(chartCanvas.dataset.regSpiritual) || 0
    ];

    // 2. Gather all 8 Attendance datasets
    const attendedData = [
        parseInt(chartCanvas.dataset.attPhysical) || 0,
        parseInt(chartCanvas.dataset.attMental) || 0,
        parseInt(chartCanvas.dataset.attFinancial) || 0,
        parseInt(chartCanvas.dataset.attSocial) || 0,
        parseInt(chartCanvas.dataset.attIntellectual) || 0,
        parseInt(chartCanvas.dataset.attOccupational) || 0,
        parseInt(chartCanvas.dataset.attEnvironmental) || 0,
        parseInt(chartCanvas.dataset.attSpiritual) || 0
    ];

    const ctx = chartCanvas.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Physical', 'Mental', 'Financial', 'Social', 'Intellectual', 'Occupational', 'Environmental', 'Spiritual'],
            datasets: [
                {
                    label: 'Registered',
                    data: registeredData,
                    backgroundColor: '#e5e7eb', // Original Light Grey Color
                    borderRadius: 4
                },
                {
                    label: 'Attended',
                    data: attendedData,
                    backgroundColor: '#02338d', // Original Dark Blue Color
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            // THE AUTO-SHRINK FIX: Waits 100ms for resizing to finish, then forces a clean layout redraw
            resizeDelay: 100, 
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        callback: function(value) {
                            if (value % 1 === 0) { return value; }
                        }
                    }
                }
            }
        }

    });
});
