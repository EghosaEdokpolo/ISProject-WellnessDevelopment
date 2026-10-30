import{n as e,t}from"./api-DbWjWFnY.js";var n=document.getElementById(`eventSelect`),r=document.getElementById(`attendeeTable`),i=document.getElementById(`attendeeRows`),a=document.getElementById(`emptyMsg`),o=document.getElementById(`errorBanner`);function s(e){return e?`✅`:`—`}function c(e){return`<span class="status status-${e}">${e}</span>`}function l(e){o.textContent=e,o.style.display=`block`}async function u(e){if(e)try{o.style.display=`none`;let n=await t(`/hr/events/${e}/attendees`),l=Array.isArray(n)?n:n.data||[];if(!l.length){r.style.display=`none`,a.style.display=`block`,a.textContent=`No attendees registered for this event yet.`;return}a.style.display=`none`,r.style.display=`table`,i.innerHTML=l.map(e=>`<tr>
            <td><strong>${e.name??`—`}</strong></td>
            <td>${e.department??`—`}</td>
            
            <!-- 1. REGISTERED COLUMN CELL: Marked checked if true -->
            <td>${s(e.registered)}</td>
            
            <!-- 2. CONSENT COLUMN CELL: Marked checked if true -->
            <td>${s(e.consent)}</td>
            
            <!-- 3. FEEDBACK COLUMN CELL: Marked checked if true -->
            <td>${s(e.feedback_provided)}</td>
            
            <td>${e.who5_pre??`—`}</td>
            <td>${e.who5_post??`—`}</td>
            <td><strong>${e.points??0}</strong></td>
            <td>${c(e.status)}</td>
        </tr>`).join(``)}catch(e){l(`Failed to load attendees: `+e.message)}}n.addEventListener(`change`,()=>u(n.value)),document.getElementById(`logoutBtn`)?.addEventListener(`click`,async n=>{n.preventDefault();try{await t(`/hr/auth/logout`,{method:`POST`})}catch{}e(),window.location.href=`/`}),n.value&&u(n.value);