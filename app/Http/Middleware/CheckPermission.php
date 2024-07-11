<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */

     
    public function handle($request, Closure $next, $permission)
    {
        if (Gate::denies($permission)) {
            return response()->json(['message' => 'Unauthorized','permission'=>$permission], 403);

        }

        return $next($request);
    }
}
