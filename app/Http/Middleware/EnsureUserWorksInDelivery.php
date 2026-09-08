<?php

namespace App\Http\Middleware;

use App\Erp\Support\LegacyRoles;
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

        // The role rename's migration cannot be run on this host, so it happens
        // here instead: one account, the first time it is used. See LegacyRoles.
        LegacyRoles::heal($user);

        // A site administrator is not automatically an engineer. Access here is
        // granted by holding a delivery role, so that closing someone's ERP
        // account does not mean editing the website's console permissions.
        abort_unless(
            $user->worksInDelivery(),
            403,
            'This account has no delivery role. Ask your practice lead or a director to enrol you.',
        );

        return $next($request);
    }
}
