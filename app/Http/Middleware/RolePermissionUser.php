<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RolePermissionUser
{

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $userRoles = $user->roles->pluck('name')->toArray();
            session(['user_role' => $userRoles]);

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            session(['user_permissions' => $userPermissions]);
        } else {
            session(['user_role' => []]);
            session(['user_permissions' => []]);
        }

        return $next($request);
    }

}
