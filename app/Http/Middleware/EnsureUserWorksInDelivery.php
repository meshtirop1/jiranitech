<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserWorksInDelivery
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('erp.login');
        }

        // A site administrator is not automatically an engineer. Access here is
        // granted by holding a delivery role, so that closing someone's ERP
        // account does not mean editing the website's console permissions.
        abort_unless(
            $user->worksInDelivery(),
            403,
            'This account has no delivery role. Ask the delivery director to enrol you.',
        );

        return $next($request);
    }
}
