// JavaScript for HR settings page
import { apiFetch, clearToken } from './api.js'; 


function showError(m) { const b = document.getElementById('errorBanner'); b.textContent = m; b.style.display = 'block'; setTimeout(() => b.style.display = 'none', 6000); }
function showSuccess(m) { const b = document.getElementById('successBanner'); b.textContent = m; b.style.display = 'block'; setTimeout(() => b.style.display = 'none', 5000); }

// Event listeners for reset buttons
document.getElementById('resetBtn').addEventListener('click', async () => {
    if (!confirm('This will zero all staff points. Are you sure?')) return;
    try {
        const r = await apiFetch('/points/reset', { method: 'POST' });
        showSuccess(`Points reset. ${r.staff_affected} staff affected.`);
    } catch (err) {
        showError('Reset failed: ' + err.message);
    }
});

// Event listener for logout button
document.getElementById('logoutBtn').addEventListener('click', function (e) {
    //stop the browser from doing anything automatically so we can run our cleanup rules first
    e.preventDefault();
    
    if (!confirm('Are you sure you want to sign out of your active session?')) return;
    
    // Clear any frontend local browser token keys using api.js helper
    clearToken();
    
    //Programmatically create a tiny temporary HTML security form to submit natively
    const logoutForm = document.createElement('form');
    logoutForm.method = 'POST';
    logoutForm.action = '/auth/logout'; // Targets your exact web.php route string

    //Inject the hidden secure CSRF protection token so Laravel approves the request
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    logoutForm.appendChild(csrfInput);
    document.body.appendChild(logoutForm);
    
    // Submit natively! This completely destroys the server-side login session cookie.
    logoutForm.submit();
});
