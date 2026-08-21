const API_BASE = (window.location.origin && window.location.protocol !== 'file:')
    ? window.location.origin + '/api'
    : 'http://localhost:8000/api';

function getUserToken() {
    return localStorage.getItem('user_token');
}

function setUserToken(token) {
    localStorage.setItem('user_token', token);
}

function clearUserToken() {
    localStorage.removeItem('user_token');
    localStorage.removeItem('user_data');
}

function getUserData() {
    const u = localStorage.getItem('user_data');
    return u ? JSON.parse(u) : null;
}

function setUserData(user) {
    localStorage.setItem('user_data', JSON.stringify(user));
}

function userEntryUrl(path) {
    return getUserToken()
        ? path
        : `login.html?redirect=${encodeURIComponent(path)}`;
}

function redirectAfterUserAuth(defaultPath) {
    const requestedPath = new URLSearchParams(window.location.search).get('redirect');
    const safePath = requestedPath && !requestedPath.includes('://') && !requestedPath.startsWith('/')
        ? requestedPath
        : defaultPath;
    window.location.replace(safePath);
}

function requireUserAuth() {
    if (!getUserToken()) {
        const currentPath = `${window.location.pathname.split('/').pop()}${window.location.search}`;
        window.location.href = `login.html?redirect=${encodeURIComponent(currentPath)}`;
        return false;
    }
    return true;
}

function userLogout() {
    fetch(`${API_BASE}/user/logout`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${getUserToken()}`, 'Accept': 'application/json' }
    }).catch(() => {});
    clearUserToken();
    window.location.href = 'login.html';
}

async function userApiGet(path) {
    const res = await fetch(`${API_BASE}${path}`, {
        headers: { 'Authorization': `Bearer ${getUserToken()}`, 'Accept': 'application/json' }
    });
    if (res.status === 401) { clearUserToken(); window.location.href = 'login.html'; return null; }
    return res.json();
}

async function userApiPost(path, data) {
    const res = await fetch(`${API_BASE}${path}`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${getUserToken()}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    });
    if (res.status === 401) { clearUserToken(); window.location.href = 'login.html'; return null; }
    return res.json();
}

async function userApiPut(path, data) {
    const res = await fetch(`${API_BASE}${path}`, {
        method: 'PUT',
        headers: {
            'Authorization': `Bearer ${getUserToken()}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    });
    if (res.status === 401) { clearUserToken(); window.location.href = 'login.html'; return null; }
    return res.json();
}

async function userApiPostNoAuth(path, data) {
    const res = await fetch(`${API_BASE}${path}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    });
    return { status: res.status, data: await res.json() };
}

function formatCurrency(amount) {
    return '$' + parseFloat(amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}
