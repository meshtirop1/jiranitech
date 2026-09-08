<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsDeliveryDirector;
use App\Http\Middleware\EnsureUserMayEnrol;
use App\Http\Middleware\EnsureUserWorksInDelivery;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            /*
             * The delivery system shares this codebase but not this hostname.
             * Binding it to a domain keeps every ERP route off the public site
             * without a second application, a second deploy or a second set of
             * credentials to keep in step.
             *
             * With ERP_DOMAIN unset — local development — the routes answer on
             * whatever host is in use, under /erp, so the whole thing is still
             * reachable from `artisan serve`.
             */
            $domain = config('erp.domain');

            Route::middleware('web')
                ->domain($domain)
                ->prefix($domain ? '' : 'erp')
                ->name('erp.')
                ->group(base_path('routes/erp.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'delivery' => EnsureUserWorksInDelivery::class,
            'delivery.director' => EnsureUserIsDeliveryDirector::class,
            'delivery.enrol' => EnsureUserMayEnrol::class,
        ]);

        /*
         * Where an unauthenticated request is sent depends on which system it
         * was trying to reach. Sending an engineer to the website's console
         * login would be a dead end.
         */
        $middleware->redirectGuestsTo(function (Request $request) {
            $erpDomain = config('erp.domain');

            $wantsErp = $erpDomain
                ? $request->getHost() === $erpDomain
                : $request->is('erp', 'erp/*');

            return $wantsErp ? route('erp.login') : route('admin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
