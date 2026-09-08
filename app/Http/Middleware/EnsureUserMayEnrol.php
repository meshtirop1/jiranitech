<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Creating accounts is a leadership right, not a directors-only one.
 *
 * A practice lead staffing their own discipline should not have to queue behind
 * the Managing Director to add an engineer. Which posts they may create is a
 * separate question, answered per-role in ErpRole::mayCreate().
 */
class EnsureUserMayEnrol
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            (bool) $request->user()?->erpRole()?->enrolsPeople(),
            403,
            'Enrolling people is reserved to leadership.',
        );

        return $next($request);
    }
}
