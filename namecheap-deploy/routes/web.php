<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

Route::get('/', function () {
    $allRoutes = RouteFacade::getRoutes();

    $routes = collect();

    foreach ($allRoutes as $route) {
        $uri = $route->uri();
        $methods = $route->methods();

        if (!str_starts_with($uri, 'api/') && !str_starts_with($uri, 'api')) {
            continue;
        }
        if (in_array('HEAD', $methods) && count($methods) === 1) {
            continue;
        }

        $action = $route->getAction();
        $controller = $action['controller'] ?? '';
        $middleware = $route->middleware();

        if (str_contains($uri, 'sanctum/')) {
            continue;
        }

        $primaryMethod = collect($methods)->first(fn ($m) => $m !== 'HEAD');
        if (!$primaryMethod) {
            continue;
        }

        $group = 'none';
        if (in_array('admin', $middleware)) {
            $group = 'admin';
        } elseif (in_array('auth.user', $middleware) || in_array('auth:sanctum', $middleware)) {
            $group = 'user';
        }

        $actionParts = explode('@', class_basename($controller));
        $actionLabel = count($actionParts) === 2
            ? class_basename($actionParts[0]) . '@' . $actionParts[1]
            : $uri;

        $displayUri = $uri;
        $prefix = 'admin/';
        if (str_starts_with($displayUri, 'api/' . $prefix)) {
            $displayUri = substr($displayUri, strlen('api/' . $prefix));
        }
        $prefix2 = 'user/';
        if (str_starts_with($displayUri, 'api/' . $prefix2)) {
            $displayUri = substr($displayUri, strlen('api/' . $prefix2));
        }

        $routes->push([
            'method' => $primaryMethod,
            'uri' => $uri,
            'action' => $actionLabel,
            'group' => $group,
        ]);
    }

    return view('dashboard', ['routes' => $routes]);
});
