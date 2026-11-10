// JavaScript for HR settings page
import { apiFetch, clearToken } from './api.js'; 


function showError(m) { const b = document.getElementById('errorBanner'); b.textContent = m; b.style.display = 'block'; setTimeout(() => b.style.display = 'none', 6000); }
function showSuccess(m) { const b = document.getElementById('successBanner'); b.textContent = m; b.style.display = 'block'; setTimeout(() => b.style.display = 'none', 5000); }

// Event listeners for reset buttons
document.getElementById('resetBtn').addEventListener('click', async () => {
    if (!confirm('This will zero all staff points. Are you sure?')) return;
    try {
        const r = await apiFetch('/hr/points/reset', { method: 'POST' });
        showSuccess(`Points reset. ${r.staff_affected} staff affected.`);
    } catch (err) {
        showError('Reset failed: ' + err.message);
    }
});

// Event listener for logout button
document.getElementById('logoutBtn').addEventListener('click', async (e) => {
    e.preventDefault();
    try { 
        await apiFetch('/hr/auth/logout', { method: 'POST' }); 
    } catch (err) {
        console.error('Logout request failed:', err);
    }
    
    // Clear any local browser values
    clearToken();
    
    // Redirect cleanly to the root login landing page
    window.location.href = '/';
});
