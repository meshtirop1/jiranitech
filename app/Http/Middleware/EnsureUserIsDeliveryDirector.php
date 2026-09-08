<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsDeliveryDirector
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            (bool) $request->user()?->erpRole()?->opensProjects(),
            403,
            'Opening an engagement is reserved to the Managing Director, the Chief Technology Officer and the Director of Delivery.',
        );

        return $next($request);
    }
}
