/**
 * HR Dashboard Impact Category Filter Engine
 * Handles real-time dashboard updates without reloading the webpage.
 */
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-pill');

    filterButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault(); // Stop any default button jumping action

            // 1. Toggle active visual look instantly
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const category = this.getAttribute('data-category');
            
            // Safe check for the base routing path meta tag
            const metaTag = document.querySelector('meta[name="impact-route"]');
            if (!metaTag) {
                console.error('Error: Meta tag name="impact-route" missing from HTML head.');
                return;
            }
            const baseUrl = metaTag.getAttribute('content');

            // 2. Target the graph and table containers
            const graphContainer = document.getElementById('graph-bars-container');
            const tableContainer = document.getElementById('table-rows-container');
            
            if (tableContainer) {
                tableContainer.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px; color:#888;">Filtering data...</td></tr>';
            }

            // 3. Fire background dynamic data call
            fetch(`${baseUrl}?category=${category}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                
                // 4. OVERWRITE TOP SUMMARY CARDS TEXT
                const cardWrapper = document.querySelector('.cards-container-wrapper');
                if (cardWrapper) {
                    const values = cardWrapper.querySelectorAll('.metric-value');
                    if (values.length >= 4) {
                        values[0].textContent = data.avgLift;
                        values[1].textContent = data.avgPreScore + '/25';
                        values[2].textContent = data.avgPostScore + '/25';
                        values[3].textContent = data.eventsInView;
                    }
                    const textNodes = cardWrapper.querySelectorAll('.card-subtext');
                    if (textNodes.length > 0) {
                        textNodes[textNodes.length - 1].textContent = `of ${data.eventsThisSemester} this semester`;
                    }
                }

                // 5. OVERWRITE 12-MONTH GRAPH BARS PILLARS
                if (graphContainer) {
                    let graphHtml = '';
                    Object.entries(data.graphBars).forEach(([monthName, bars]) => {
                        graphHtml += `
                            <div class="impact-month-column">
                                <div class="impact-pair-group">
                                    <div class="impact-pillar bar-pre" style="--bar-height: ${bars.pre_height}%;"></div>
                                    <div class="impact-pillar bar-post" style="--bar-height: ${bars.post_height}%;"></div>
                                </div>
                                <span class="impact-xaxis-label">${monthName}</span>
                                <div class="impact-hover-tooltip">
                                    <h4 class="impact-tooltip-title">${monthName}</h4>
                                    <p class="impact-tooltip-row text-light">Pre-event: <span>${bars.pre_raw}</span></p>
                                    <p class="impact-tooltip-row text-blue">Post-event: <span>${bars.post_raw}</span></p>
                                </div>
                            </div>
                        `;
                    });
                    graphContainer.innerHTML = graphHtml;
                }

                // 6. OVERWRITE BOTTOM COMPONENT TABLE ROWS
                if (tableContainer) {
                    let tableHtml = '';
                    if (data.eventsTableRows.length === 0) {
                        tableHtml = `
                            <tr>
                                <td colspan="6" class="center-text dash-placeholder" style="text-align: center; padding: 30px; color:#9ca3af; font-weight:bold;">
                                    No active event evaluation logs found for this filter category.
                                </td>
                            </tr>
                        `;
                    } else {
                        data.eventsTableRows.forEach(row => {
                            tableHtml += `
                                <tr>
                                    <td class="event-name-cell" style="text-align:left; font-weight:600;">${row.name}</td>
                                    <td class="center-text metric-number" style="text-align:center;">${row.participants}</td>
                                    <td class="center-text metric-number" style="text-align:center;">${row.pre_avg}</td>
                                    <td class="center-text metric-number" style="text-align:center;">${row.post_avg}</td>
                                    <td class="center-text font-bold metric-number" style="text-align:center; font-weight:700;">${row.lift}</td>
                                    <td class="center-text metric-number" style="text-align:center;">${row.follow_up}</td>
                                </tr>
                            `;
                        });
                    }
                    tableContainer.innerHTML = tableHtml;
                }
            })
            .catch(error => {
                console.error('Error handling AJAX filter request:', error);
                if (tableContainer) {
                    tableContainer.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px; color:red;">Failed to filter data.</td></tr>';
                }
            });
        });
    });
});
