<?php

namespace App\Http\Middleware;

use App\Models\AdminActivityLog;
use App\Support\AdminActivity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminNavigation
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = auth()->user();

        if (! $user) {
            return $response;
        }

        $routeName = optional($request->route())->getName();

        if (! $routeName || ! str_starts_with($routeName, 'filament.admin.')) {
            return $response;
        }

        if (in_array($routeName, ['filament.admin.auth.login'], true)) {
            return $response;
        }

        if (! in_array($request->method(), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $response;
        }

        if (str_contains($request->path(), 'livewire-ca')) {
            return $response;
        }

        AdminActivity::log(
            AdminActivityLog::TYPE_NAVIGATION,
            'Admin panel etkilesimi: ' . $routeName,
            $user->id,
            properties: [
                'query' => $request->query(),
            ],
        );

        return $response;
    }
}
