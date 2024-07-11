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
        try {
            if (Gate::denies($permission)) {

                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return $next($request);
        } catch (\Lcobucci\JWT\Validation\RequiredConstraintsViolated $e) {
            Log::error('Token JWT invalido: ' . $e->getMessage());
            return response()->json(['message' => 'Errore di autenticazione'], 401);
        } catch (\Exception $e) {
            Log::error('Eccezione durante la verifica del token JWT: ' . $e->getMessage());
            return response()->json(['message' => 'Errore di autenticazione'], 401);
        }
    }
}
