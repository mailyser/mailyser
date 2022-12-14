<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionMiddleware
{
    protected array $excludedRoutes = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if (! $request->user()?->hasAccess() && ! $request->routeIs($this->excludedRoutes)) {
            return redirect()->route('filament.pages.subscription');
        }

        return $next($request);
    }
}
