<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogEntryActivity
{
    /**
     * Routes that already write detailed audit logs in their controllers.
     *
     * @var array<int, string>
     */
    private const SKIP_ROUTES = [
        'accounting.transactions.store',
        'accounting.transactions.update',
        'accounting.transactions.destroy',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $routeName = $request->route()?->getName();

        if (
            $request->user()
            && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            && $response->getStatusCode() < 400
            && ! in_array($routeName, self::SKIP_ROUTES, true)
        ) {
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'route_' . strtolower($request->method()),
                'description' => 'Route: ' . ($routeName ?? 'unknown'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'occurred_at' => now(),
            ]);
        }

        return $response;
    }
}
