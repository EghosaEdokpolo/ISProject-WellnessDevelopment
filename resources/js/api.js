/**
 * Global API Fetch Helper
 */
export async function apiFetch(url, options = {}) {
    // 1. Initialize headers if they don't exist yet
    const headers = options.headers || {};
    
    // Tell the server we are sending and expecting JSON data
    headers['Content-Type'] = 'application/json';
    headers['Accept'] = 'application/json';

    // Go out to the HTML document head, search for the meta tag named "csrf-token"
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // If successfully found it, attach it to our outgoing headers!
    if (token) {
        // Attach the CSRF token to the headers for laravel to verify
        headers['X-CSRF-TOKEN'] = token; 
    }

    // 3. Send the actual request out to the internet
    const response = await fetch(url, {
        ...options,
        headers: headers
    });

    // 4. Error Checking
    if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.message || `Server Error: ${response.status}`);
    }

    // Return the clean data back to your settings page
    return response.json();
}

/**
 * Global Token Clear Helper (For Logout)
 */
export function clearToken() {
    localStorage.removeItem('auth_token');
    sessionStorage.clear();
}
