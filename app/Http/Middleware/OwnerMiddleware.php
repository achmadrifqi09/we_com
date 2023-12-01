<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roleId = Auth::user()->role_id;

        if (Role::find($roleId)->name !== 'Owner') {
            return response()->json([
                'errors' => [
                    'message' => [
                        'forbidden'
                    ]
                ]
            ])->setStatusCode(403);
        }

        return $next($request);
    }
}
