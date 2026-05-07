<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FinancialTargetController;
use App\Http\Controllers\PhysicalAccomplishmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Project Registry
    Route::resource('projects', ProjectController::class);

    // Financial Targets (nested under project)
    Route::prefix('projects/{project}/financial-targets')->name('projects.financial-targets.')->group(function () {
        Route::get('/',         [FinancialTargetController::class, 'index'])->name('index');
        Route::get('/create',   [FinancialTargetController::class, 'create'])->name('create');
        Route::post('/',        [FinancialTargetController::class, 'store'])->name('store');
        Route::get('/{financialTarget}/edit', [FinancialTargetController::class, 'edit'])->name('edit');
        Route::put('/{financialTarget}',      [FinancialTargetController::class, 'update'])->name('update');
        Route::delete('/{financialTarget}',   [FinancialTargetController::class, 'destroy'])->name('destroy');
        Route::post('/import',  [FinancialTargetController::class, 'import'])->name('import');
    });

    // Document upload & download (scoped to project for upload)
    Route::post('/projects/{project}/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Physical Accomplishments (nested under project)
    Route::prefix('projects/{project}/physical-accomplishments')->name('projects.physical-accomplishments.')->group(function () {
        Route::get('/',         [PhysicalAccomplishmentController::class, 'index'])->name('index');
        Route::get('/create',   [PhysicalAccomplishmentController::class, 'create'])->name('create');
        Route::post('/',        [PhysicalAccomplishmentController::class, 'store'])->name('store');
        Route::get('/{physicalAccomplishment}/edit', [PhysicalAccomplishmentController::class, 'edit'])->name('edit');
        Route::put('/{physicalAccomplishment}',      [PhysicalAccomplishmentController::class, 'update'])->name('update');
        Route::delete('/{physicalAccomplishment}',   [PhysicalAccomplishmentController::class, 'destroy'])->name('destroy');
        Route::post('/import',  [PhysicalAccomplishmentController::class, 'import'])->name('import');
    });

    // Verification Queue
    Route::get('/verification', [VerificationController::class, 'index'])->name('verification.index');
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/financial/excel', [ReportController::class, 'financialExcel'])->name('reports.financial.excel');
    Route::post('/reports/financial/pdf',   [ReportController::class, 'financialPdf'])->name('reports.financial.pdf');
    Route::post('/reports/physical/excel',  [ReportController::class, 'physicalExcel'])->name('reports.physical.excel');
    Route::post('/reports/physical/pdf',    [ReportController::class, 'physicalPdf'])->name('reports.physical.pdf');
    Route::post('/reports/audit/excel',     [ReportController::class, 'auditExcel'])->name('reports.audit.excel');
    Route::post('/reports/audit/pdf',       [ReportController::class, 'auditPdf'])->name('reports.audit.pdf');
    // User Management (admin only)
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
