const API_BASE = (window.location.origin && window.location.protocol !== 'file:')
    ? window.location.origin + '/api'
    : 'http://localhost:8000/api';

function getAuthToken() {
    return localStorage.getItem('admin_token');
}

function setAuthToken(token) {
    localStorage.setItem('admin_token', token);
}

function clearAuthToken() {
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_user');
}

function adminLogout() {
    clearAuthToken();
    window.location.href = 'login.html';
}

function getAdminUser() {
    const u = localStorage.getItem('admin_user');
    return u ? JSON.parse(u) : null;
}

function setAdminUser(user) {
    localStorage.setItem('admin_user', JSON.stringify(user));
}

function requireAuth() {
    if (!getAuthToken()) {
        window.location.href = 'login.html';
        return false;
    }
    return true;
}

async function apiGet(path) {
    const res = await fetch(`${API_BASE}${path}`, {
        headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Accept': 'application/json' }
    });
    if (res.status === 401) { clearAuthToken(); window.location.href = 'login.html'; return null; }
    return res.json();
}

async function apiPost(path, data = {}) {
    const res = await fetch(`${API_BASE}${path}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    });
    if (res.status === 401) { clearAuthToken(); window.location.href = 'login.html'; return null; }
    return res.json();
}

async function apiPut(path, data) {
    const res = await fetch(`${API_BASE}${path}`, {
        method: 'PUT',
        headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    });
    if (res.status === 401) { clearAuthToken(); window.location.href = 'login.html'; return null; }
    return res.json();
}

async function apiPatch(path, data = {}) {
    const res = await fetch(`${API_BASE}${path}`, {
        method: 'PATCH',
        headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    });
    if (res.status === 401) { clearAuthToken(); window.location.href = 'login.html'; return null; }
    return res.json();
}

async function apiDelete(path) {
    const res = await fetch(`${API_BASE}${path}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${getAuthToken()}`, 'Accept': 'application/json' }
    });
    if (res.status === 401) { clearAuthToken(); window.location.href = 'login.html'; return null; }
    return res.json();
}

function formatCurrency(amount) {
    return '$' + parseFloat(amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function formatDateTime(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function timeAgo(dateStr) {
    if (!dateStr) return '-';
    const seconds = Math.floor((new Date() - new Date(dateStr)) / 1000);
    if (seconds < 60) return 'just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' min ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
    return Math.floor(seconds / 86400) + ' days ago';
}

function getInitials(name) {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
}

function getAvatarColor(name) {
    const colors = ['from-blue-500 to-blue-700', 'from-green-500 to-emerald-700', 'from-purple-500 to-purple-700', 'from-cyan-500 to-cyan-700', 'from-yellow-500 to-yellow-700', 'from-red-500 to-red-700', 'from-pink-500 to-pink-700', 'from-indigo-500 to-indigo-700'];
    let hash = 0;
    for (let i = 0; i < (name || '').length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
    return colors[Math.abs(hash) % colors.length];
}

function exportCSV(headers, rows, filename) {
    const csvContent = [headers, ...rows].map(row => row.map(cell => {
        const str = String(cell ?? '').replace(/"/g, '""');
        return str.includes(',') || str.includes('"') || str.includes('\n') ? `"${str}"` : str;
    }).join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename || 'export.csv';
    link.click();
    URL.revokeObjectURL(link.href);
}
