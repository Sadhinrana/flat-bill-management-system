<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class MultiTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin users can access everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // House owners can only access their own data
        if ($user->isHouseOwner()) {
            // Set the building_id in the request for scoping
            $request->merge(['user_building_id' => $user->ownedBuilding->id ?? null]);

            // Add global scope for house owners
            $this->addGlobalScope($user->ownedBuilding->id ?? null);
        }

        return $next($request);
    }

    /**
     * Add global scope for house owners to limit data access
     */
    private function addGlobalScope($buildingId)
    {
        // This will be handled in the models using the scopes we defined
        // The middleware just ensures the building_id is available in the request
    }
}
