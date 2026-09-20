// public/js/api.js

const TOKEN_KEY = 'wpmis_token';

export function getToken() {
    return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token) {
    localStorage.setItem(TOKEN_KEY, token);
}

export function clearToken() {
    localStorage.removeItem(TOKEN_KEY);
}

export async function apiFetch(path, options = {}) {
    const res = await fetch(`/api/v1${path}`, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${getToken()}`,
            ...(options.headers || {}),
        },
    });

    if (res.status === 401) {
        clearToken();
        window.location.href = '/';
        return null;
    }

    if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error(err.message || `Request failed: ${res.status}`);
    }

    return res.json();
}