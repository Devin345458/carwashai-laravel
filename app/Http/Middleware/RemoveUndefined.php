<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveUndefined
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (str_contains($request->server->get('REQUEST_URI'), "undefined")) {
            $request->server->set('REQUEST_URI', preg_replace('/undefined/', '', $request->server->get('REQUEST_URI')));
        };
        return $next($request);
    }

}
