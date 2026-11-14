document.addEventListener('DOMContentLoaded', function () {
    let impactChartInstance = null;

    function renderChart(labels, preData, postData) {
        const canvas = document.getElementById('impactCanvasEngine');
        if (!canvas) return;

        if (impactChartInstance) {
            impactChartInstance.destroy();
        }

        impactChartInstance = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels, 
                datasets: [
                    { label: 'Pre-event', data: preData, backgroundColor: '#e5e7eb', borderRadius: 4 },
                    { label: 'Post-event', data: postData, backgroundColor: '#02338d', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                resizeDelay: 100,
                plugins: {
                    legend: {
                        position: 'bottom', // Moves legend down below chart
                        labels: { 
                            font: { 
                                family: "'Lato', 'Segoe UI', 'Arial', 'Helvetica', sans-serif", 
                                size: 13,
                                weight: '700'
                            } 
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Lato', 'Segoe UI', 'Arial', 'Helvetica', sans-serif", size: 11 } }
                    },
                    y: { 
                        beginAtZero: true, 
                        max: 25,
                        grid: { borderDash: [5, 5] },
                        ticks: { font: { family: "'Lato', 'Segoe UI', 'Arial', 'Helvetica', sans-serif", size: 11 } }
                    }
                }
            }
        });
    }

    // 1. Initial Load Chart Initialization
    const initialCanvas = document.getElementById('impactCanvasEngine');
    if (initialCanvas) {
        const labels = JSON.parse(initialCanvas.getAttribute('data-categories')) || [];
        const preData = JSON.parse(initialCanvas.getAttribute('data-pre-avgs')) || [];
        const postData = JSON.parse(initialCanvas.getAttribute('data-post-avgs')) || [];
        renderChart(labels, preData, postData);
    }

    // 2. Click Handler for Filter Pills
    const filterPills = document.querySelectorAll('.filter-pill-btn');
    
    filterPills.forEach(pill => {
        pill.addEventListener('click', function (e) {
            e.preventDefault();

            filterPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');

            const category = this.getAttribute('data-category');
            const tableBody = document.getElementById('table-rows-container');

            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px; color:#6b7280;">Updating views...</td></tr>';
            }

            fetch(`/impact-analytics?category=${category}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                // Adjust visibility of the High/Low banner row
                const bannerRow = document.getElementById('highest-lowest-banner-row');
                if (bannerRow) {
                    bannerRow.style.display = (category === 'all' || !category) ? 'block' : 'none';
                    if (category === 'all' || !category) {
                        document.getElementById('highest-dim-text').textContent = data.highestDim;
                        document.getElementById('lowest-dim-text').textContent = data.lowestDim;
                    }
                }

                // Update KPI Card metric values
                const cardContainer = document.getElementById('impact-kpi-cards-wrapper');
                if (cardContainer) {
                    const metrics = cardContainer.querySelectorAll('.metric-value');
                    if (metrics.length >= 4) {
                        metrics[0].textContent = data.avgLift;
                        metrics[1].textContent = data.avgPreScore;
                        metrics[2].textContent = data.avgPostScore;
                        metrics[3].textContent = data.eventsInView;
                    }
                }

                // Update Chart JS Layout Vectors
                const updatedLabels = Object.keys(data.graphBars);
                const updatedPre = Object.values(data.graphBars).map(v => v.pre_avg);
                const updatedPost = Object.values(data.graphBars).map(v => v.post_avg);
                renderChart(updatedLabels, updatedPre, updatedPost);

                // Re-render data table records markup rows
                if (tableBody) {
                    let html = '';
                    if (data.eventsTableRows.length === 0) {
                        html = '<tr><td colspan="6" style="text-align:center; padding:30px; color:#9ca3af;">No event logs found for this criteria.</td></tr>';
                    } else {
                        data.eventsTableRows.forEach(row => {
                            const val = parseFloat(row.lift);
                            const cls = val > 0 ? 'lift-positive' : (val < 0 ? 'lift-negative' : '');
                            
                            html += `
                                <tr>
                                    <td style="text-align: left;"><strong>${row.name}</strong></td>
                                    <td>${row.category}</td>
                                    <td>${row.pre_avg}</td>
                                    <td>${row.post_avg}</td>
                                    <td class="${cls}" style="font-weight:700;">${row.lift}</td>
                                    <td style="font-weight: bold; color: #10b981;">${row.survey_status}</td>
                                    <td style="font-weight: bold; color: ${statusColor};">${row.survey_status}</td>
                                </tr>
                            `;
                        });
                    }
                    tableBody.innerHTML = html;
                }
            })
            .catch(err => console.error("AJAX Fetch Engine Request Error:", err));
        });
    });
});
