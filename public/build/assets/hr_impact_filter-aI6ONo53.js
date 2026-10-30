document.addEventListener(`DOMContentLoaded`,function(){let e=document.querySelectorAll(`.filter-pill`);e.forEach(t=>{t.addEventListener(`click`,function(t){t.preventDefault(),e.forEach(e=>e.classList.remove(`active`)),this.classList.add(`active`);let n=this.getAttribute(`data-category`),r=document.querySelector(`meta[name="impact-route"]`);if(!r){console.error(`Error: Meta tag name="impact-route" missing from HTML head.`);return}let i=r.getAttribute(`content`),a=document.getElementById(`graph-bars-container`),o=document.getElementById(`table-rows-container`);o&&(o.innerHTML=`<tr><td colspan="6" style="text-align:center; padding:20px; color:#888;">Filtering data...</td></tr>`),fetch(`${i}?category=${n}`,{headers:{"X-Requested-With":`XMLHttpRequest`}}).then(e=>e.json()).then(e=>{let t=document.querySelector(`.cards-container-wrapper`);if(t){let n=t.querySelectorAll(`.metric-value`);n.length>=4&&(n[0].textContent=e.avgLift,n[1].textContent=e.avgPreScore+`/25`,n[2].textContent=e.avgPostScore+`/25`,n[3].textContent=e.eventsInView);let r=t.querySelectorAll(`.card-subtext`);r.length>0&&(r[r.length-1].textContent=`of ${e.eventsThisSemester} this semester`)}if(a){let t=``;Object.entries(e.graphBars).forEach(([e,n])=>{t+=`
                            <div class="impact-month-column">
                                <div class="impact-pair-group">
                                    <div class="impact-pillar bar-pre" style="--bar-height: ${n.pre_height}%;"></div>
                                    <div class="impact-pillar bar-post" style="--bar-height: ${n.post_height}%;"></div>
                                </div>
                                <span class="impact-xaxis-label">${e}</span>
                                <div class="impact-hover-tooltip">
                                    <h4 class="impact-tooltip-title">${e}</h4>
                                    <p class="impact-tooltip-row text-light">Pre-event: <span>${n.pre_raw}</span></p>
                                    <p class="impact-tooltip-row text-blue">Post-event: <span>${n.post_raw}</span></p>
                                </div>
                            </div>
                        `}),a.innerHTML=t}if(o){let t=``;e.eventsTableRows.length===0?t=`
                            <tr>
                                <td colspan="6" class="center-text dash-placeholder" style="text-align: center; padding: 30px; color:#9ca3af; font-weight:bold;">
                                    No active event evaluation logs found for this filter category.
                                </td>
                            </tr>
                        `:e.eventsTableRows.forEach(e=>{t+=`
                                <tr>
                                    <td class="event-name-cell" style="text-align:left; font-weight:600;">${e.name}</td>
                                    <td class="center-text metric-number" style="text-align:center;">${e.participants}</td>
                                    <td class="center-text metric-number" style="text-align:center;">${e.pre_avg}</td>
                                    <td class="center-text metric-number" style="text-align:center;">${e.post_avg}</td>
                                    <td class="center-text font-bold metric-number" style="text-align:center; font-weight:700;">${e.lift}</td>
                                    <td class="center-text metric-number" style="text-align:center;">${e.follow_up}</td>
                                </tr>
                            `}),o.innerHTML=t}}).catch(e=>{console.error(`Error handling AJAX filter request:`,e),o&&(o.innerHTML=`<tr><td colspan="6" style="text-align:center; padding:20px; color:red;">Failed to filter data.</td></tr>`)})})})});