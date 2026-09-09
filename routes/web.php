<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EngagementModelController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\PillarController;
use App\Http\Controllers\PlatformReferenceController;
use App\Http\Controllers\RfpSubmissionController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
| URL taxonomy per JTS-WEB-IA-001 section 2.3.
|
| Pillar segments are stable identifiers and must not be renamed after launch.
| Leaf service slugs may be revised, but only behind a 301.
*/

Route::get('/', HomeController::class)->name('home');

// Crawler-facing files. Generated rather than static so neither can outlive a
// change of domain, as public/robots.txt did.
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

// Capability taxonomy. Depth from home to any leaf is three clicks.
Route::get('/services', [PillarController::class, 'index'])->name('services.index');
Route::get('/services/{pillar}', [PillarController::class, 'show'])->name('pillars.show');
Route::get('/services/{pillar}/{service}', [ServiceController::class, 'show'])
    ->scopeBindings()
    ->name('services.show');

// Commercial structure.
Route::get('/engagement-models', [EngagementModelController::class, 'index'])->name('engagement-models.index');
Route::get('/engagement-models/{engagementModel}', [EngagementModelController::class, 'show'])->name('engagement-models.show');

// Sector language for non-technical buyers.
Route::get('/industries', [IndustryController::class, 'index'])->name('industries.index');
Route::get('/industries/{industry}', [IndustryController::class, 'show'])->name('industries.show');

// Operator proof.
Route::get('/platforms', [PlatformReferenceController::class, 'index'])->name('platforms.index');
Route::get('/platforms/{platformReference}', [PlatformReferenceController::class, 'show'])->name('platforms.show');

// Technical authority.
Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('/insights/{insight}', [InsightController::class, 'show'])->name('insights.show');

// Governance and continuity proof.
Route::prefix('company')->name('company.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/about', [CompanyController::class, 'about'])->name('about');
    Route::get('/governance', [CompanyController::class, 'governance'])->name('governance');
    Route::get('/leadership', [CompanyController::class, 'leadership'])->name('leadership');
    Route::get('/delivery-model', [CompanyController::class, 'deliveryModel'])->name('delivery-model');
    Route::get('/careers', [CompanyController::class, 'careers'])->name('careers');
});

// Conversion.
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::get('/contact/engagement-desk', [ContactController::class, 'engagementDesk'])->name('contact.engagement-desk');
Route::get('/contact/request-for-proposal', [RfpSubmissionController::class, 'create'])->name('rfp.create');
Route::post('/contact/request-for-proposal', [RfpSubmissionController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('rfp.store');
Route::get('/contact/request-for-proposal/{rfpSubmission}', [RfpSubmissionController::class, 'show'])
    ->middleware('signed')
    ->name('rfp.confirmation');

Route::prefix('legal')->name('legal.')->group(function () {
    Route::get('/privacy-notice', [LegalController::class, 'privacy'])->name('privacy');
    Route::get('/terms-of-engagement', [LegalController::class, 'terms'])->name('terms');
    Route::get('/data-processing-addendum', [LegalController::class, 'dataProcessing'])->name('data-processing');
    Route::get('/sub-processors', [LegalController::class, 'subProcessors'])->name('sub-processors');
    Route::get('/responsible-disclosure', [LegalController::class, 'disclosure'])->name('disclosure');
    Route::get('/accessibility-statement', [LegalController::class, 'accessibility'])->name('accessibility');
});

/*
| Admin console.
|
| The host has no shell, so the first administrator is created through a setup route
| that closes permanently once any admin exists. Everything else sits behind auth plus
| an is_admin check.
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/setup', [Admin\SetupController::class, 'create'])->name('setup');
    Route::post('/setup', [Admin\SetupController::class, 'store'])->name('setup.store');

    Route::get('/login', [Admin\SessionController::class, 'create'])->name('login');
    Route::post('/login', [Admin\SessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::post('/logout', [Admin\SessionController::class, 'destroy'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('/settings', [Admin\SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');

        Route::get('/service-levels', [Admin\SlaTierController::class, 'edit'])->name('sla.edit');
        Route::put('/service-levels', [Admin\SlaTierController::class, 'update'])->name('sla.update');

        // Pillars, services, industries and engagement models. One controller,
        // because the only thing that differs between them is the field list.
        Route::get('/content/{type}', [Admin\ContentController::class, 'index'])->name('content.index');
        Route::post('/content/{type}', [Admin\ContentController::class, 'store'])->name('content.store');
        Route::put('/content/{type}/{id}', [Admin\ContentController::class, 'update'])->name('content.update');
        Route::delete('/content/{type}/{id}', [Admin\ContentController::class, 'destroy'])->name('content.destroy');

        Route::get('/metrics', [Admin\MetricController::class, 'index'])->name('metrics.index');
        Route::put('/metrics/{metric}', [Admin\MetricController::class, 'update'])->name('metrics.update');
        Route::post('/metrics', [Admin\MetricController::class, 'store'])->name('metrics.store');
        Route::delete('/metrics/{metric}', [Admin\MetricController::class, 'destroy'])->name('metrics.destroy');

        Route::get('/compliance', [Admin\ComplianceClaimController::class, 'index'])->name('compliance.index');
        Route::put('/compliance/{compliance}', [Admin\ComplianceClaimController::class, 'update'])->name('compliance.update');
        Route::post('/compliance', [Admin\ComplianceClaimController::class, 'store'])->name('compliance.store');
        Route::delete('/compliance/{compliance}', [Admin\ComplianceClaimController::class, 'destroy'])->name('compliance.destroy');

        Route::get('/platforms', [Admin\PlatformReferenceController::class, 'index'])->name('platforms.index');
        Route::put('/platforms/{platform}', [Admin\PlatformReferenceController::class, 'update'])->name('platforms.update');
        Route::post('/platforms', [Admin\PlatformReferenceController::class, 'store'])->name('platforms.store');
        Route::delete('/platforms/{platform}', [Admin\PlatformReferenceController::class, 'destroy'])->name('platforms.destroy');

        Route::get('/insights', [Admin\InsightController::class, 'index'])->name('insights.index');
        Route::get('/insights/create', [Admin\InsightController::class, 'create'])->name('insights.create');
        Route::post('/insights', [Admin\InsightController::class, 'store'])->name('insights.store');
        Route::get('/insights/{insight}/edit', [Admin\InsightController::class, 'edit'])->name('insights.edit');
        Route::put('/insights/{insight}', [Admin\InsightController::class, 'update'])->name('insights.update');
        Route::delete('/insights/{insight}', [Admin\InsightController::class, 'destroy'])->name('insights.destroy');

        Route::get('/team', [Admin\TeamMemberController::class, 'index'])->name('team.index');
        Route::put('/team/{team}', [Admin\TeamMemberController::class, 'update'])->name('team.update');
        Route::post('/team', [Admin\TeamMemberController::class, 'store'])->name('team.store');
        Route::delete('/team/{team}', [Admin\TeamMemberController::class, 'destroy'])->name('team.destroy');

        Route::get('/jobs', [Admin\JobOpeningController::class, 'index'])->name('jobs.index');
        Route::put('/jobs/{job}', [Admin\JobOpeningController::class, 'update'])->name('jobs.update');
        Route::post('/jobs', [Admin\JobOpeningController::class, 'store'])->name('jobs.store');
        Route::delete('/jobs/{job}', [Admin\JobOpeningController::class, 'destroy'])->name('jobs.destroy');

        Route::get('/rfp', [Admin\RfpInboxController::class, 'index'])->name('rfp.index');
        Route::get('/rfp/{rfp}', [Admin\RfpInboxController::class, 'show'])->name('rfp.show');
        Route::put('/rfp/{rfp}/acknowledge', [Admin\RfpInboxController::class, 'acknowledge'])->name('rfp.acknowledge');
    });
});
