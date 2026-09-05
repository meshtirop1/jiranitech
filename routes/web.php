<?php

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
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

/*
| URL taxonomy per JTS-WEB-IA-001 section 2.3.
|
| Pillar segments are stable identifiers and must not be renamed after launch.
| Leaf service slugs may be revised, but only behind a 301.
*/

Route::get('/', HomeController::class)->name('home');

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
    Route::get('/responsible-disclosure', [LegalController::class, 'disclosure'])->name('disclosure');
    Route::get('/accessibility-statement', [LegalController::class, 'accessibility'])->name('accessibility');
});
