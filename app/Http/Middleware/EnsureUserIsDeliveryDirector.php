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
            (bool) $request->user()?->erpRole()?->administersDelivery(),
            403,
            'Only the delivery director can open projects, enrol people or change roles.',
        );

        return $next($request);
    }
}
