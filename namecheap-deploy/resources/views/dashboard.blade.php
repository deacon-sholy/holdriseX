<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HoldRiseX API Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #0a0e17; color: #e2e8f0; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #1a1f35 0%, #0d1321 100%); border-bottom: 1px solid #1e293b; padding: 30px 40px; }
        .header h1 { font-size: 28px; color: #f8fafc; margin-bottom: 6px; }
        .header p { color: #94a3b8; font-size: 14px; }
        .status { display: inline-flex; align-items: center; gap: 8px; background: #064e3b; color: #6ee7b7; padding: 6px 14px; border-radius: 20px; font-size: 13px; margin-top: 12px; }
        .status::before { content: ''; width: 8px; height: 8px; background: #34d399; border-radius: 50%; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        .container { max-width: 1200px; margin: 0 auto; padding: 30px 40px; }
        .info-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px; }
        .info-card { background: #111827; border: 1px solid #1e293b; border-radius: 10px; padding: 18px; }
        .info-card .label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .info-card .value { font-size: 24px; font-weight: 700; color: #f8fafc; margin-top: 4px; }
        .info-card .value.green { color: #34d399; }
        .info-card .value.blue { color: #60a5fa; }
        .info-card .value.purple { color: #a78bfa; }
        .info-card .value.amber { color: #fbbf24; }
        .section { margin-bottom: 30px; }
        .section-header { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; cursor: pointer; user-select: none; }
        .section-header h2 { font-size: 18px; color: #f8fafc; }
        .badge { font-size: 12px; padding: 3px 10px; border-radius: 12px; font-weight: 600; }
        .badge.public { background: #064e3b; color: #6ee7b7; }
        .badge.protected { background: #1e3a5f; color: #60a5fa; }
        .badge.admin { background: #4c1d95; color: #c4b5fd; }
        .toggle-icon { color: #64748b; font-size: 12px; transition: transform 0.2s; }
        .section-header.collapsed .toggle-icon { transform: rotate(-90deg); }
        table { width: 100%; border-collapse: collapse; background: #111827; border-radius: 10px; overflow: hidden; border: 1px solid #1e293b; }
        th { background: #1a2332; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; text-align: left; padding: 12px 16px; }
        td { padding: 10px 16px; border-top: 1px solid #1e293b; font-size: 14px; vertical-align: middle; }
        tr:hover td { background: #162032; }
        .method { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 700; min-width: 60px; text-align: center; font-family: monospace; }
        .method.GET { background: #064e3b; color: #6ee7b7; }
        .method.POST { background: #1e3a5f; color: #60a5fa; }
        .method.PUT, .method.PATCH { background: #713f12; color: #fbbf24; }
        .method.DELETE { background: #7f1d1d; color: #fca5a5; }
        .uri { font-family: 'SF Mono', 'Fira Code', monospace; color: #e2e8f0; font-size: 13px; }
        .controller { color: #64748b; font-size: 12px; }
        .auth-note { font-size: 11px; color: #64748b; margin-top: 30px; text-align: center; padding: 16px; border-top: 1px solid #1e293b; }
        .try-btn { display: inline-block; padding: 4px 10px; background: #1e293b; color: #60a5fa; border: 1px solid #334155; border-radius: 4px; text-decoration: none; font-size: 12px; transition: all 0.2s; }
        .try-btn:hover { background: #334155; color: #93c5fd; }
    </style>
</head>
<body>
    <div class="header">
        <h1>HoldRiseX Trading Platform</h1>
        <p>RESTful API Backend — Laravel {{ app()->version() }} / PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}</p>
        <div class="status">Server Running on localhost:8000</div>
    </div>
    <div class="container">
        <div class="info-cards">
            <div class="info-card">
                <div class="label">Total Endpoints</div>
                <div class="value green">{{ count($routes) }}</div>
            </div>
            <div class="info-card">
                <div class="label">Public Routes</div>
                <div class="value blue">{{ $routes->where('middleware', 'none')->count() }}</div>
            </div>
            <div class="info-card">
                <div class="label">User Routes</div>
                <div class="value purple">{{ $routes->where('group', 'user')->count() }}</div>
            </div>
            <div class="info-card">
                <div class="label">Admin Routes</div>
                <div class="value amber">{{ $routes->where('group', 'admin')->count() }}</div>
            </div>
        </div>

        {{-- Public --}}
        <div class="section">
            <div class="section-header" onclick="toggle('public')">
                <span class="toggle-icon" id="icon-public">▼</span>
                <h2>Public Endpoints</h2>
                <span class="badge public">No Auth</span>
            </div>
            <table id="public">
                <thead><tr><th>Method</th><th>URI</th><th>Action</th><th></th></tr></thead>
                <tbody>
                    @foreach($routes->where('middleware', 'none') as $route)
                    <tr>
                        <td><span class="method {{ $route['method'] }}">{{ $route['method'] }}</span></td>
                        <td class="uri">/api{{ $route['uri'] }}</td>
                        <td class="controller">{{ $route['action'] }}</td>
                        <td><a href="/api{{ $route['uri'] }}" class="try-btn" target="_blank">Try</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- User --}}
        <div class="section">
            <div class="section-header" onclick="toggle('user')">
                <span class="toggle-icon" id="icon-user">▼</span>
                <h2>User Endpoints</h2>
                <span class="badge protected">Auth + User</span>
            </div>
            <table id="user">
                <thead><tr><th>Method</th><th>URI</th><th>Action</th><th></th></tr></thead>
                <tbody>
                    @foreach($routes->where('group', 'user') as $route)
                    <tr>
                        <td><span class="method {{ $route['method'] }}">{{ $route['method'] }}</span></td>
                        <td class="uri">/api{{ $route['uri'] }}</td>
                        <td class="controller">{{ $route['action'] }}</td>
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Admin --}}
        <div class="section">
            <div class="section-header" onclick="toggle('admin')">
                <span class="toggle-icon" id="icon-admin">▼</span>
                <h2>Admin Endpoints</h2>
                <span class="badge admin">Auth + Admin</span>
            </div>
            <table id="admin">
                <thead><tr><th>Method</th><th>URI</th><th>Action</th><th></th></tr></thead>
                <tbody>
                    @foreach($routes->where('group', 'admin') as $route)
                    <tr>
                        <td><span class="method {{ $route['method'] }}">{{ $route['method'] }}</span></td>
                        <td class="uri">/api{{ $route['uri'] }}</td>
                        <td class="controller">{{ $route['action'] }}</td>
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="auth-note">
            Protected routes require a Bearer token in the <code>Authorization</code> header.<br>
            Example: <code>Authorization: Bearer YOUR_TOKEN_HERE</code>
        </div>
    </div>

    <script>
        function toggle(id) {
            const table = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            const header = icon.parentElement;
            if (table.style.display === 'none') {
                table.style.display = '';
                icon.textContent = '▼';
                header.classList.remove('collapsed');
            } else {
                table.style.display = 'none';
                icon.textContent = '▶';
                header.classList.add('collapsed');
            }
        }
    </script>
</body>
</html>
