<?php

use App\Http\Controllers\DocumentFileController;
use App\Livewire\Admin\AuditLogViewer;
use App\Livewire\Admin\RetentionPolicies;
use App\Livewire\Admin\SecuritySettings;
use App\Livewire\Admin\SystemHealth;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\MfaChallenge;
use App\Livewire\BulkImport\ImportMetadata;
use App\Livewire\BulkImport\MigrationBatchView;
use App\Livewire\Dashboard;
use App\Livewire\Documents\DocumentViewer;
use App\Livewire\Documents\RegisterDocument;
use App\Livewire\Documents\SearchDocuments;
use App\Livewire\Documents\ShowDocument;
use App\Livewire\Documents\UploadPdf;
use App\Livewire\Processing\ProcessingDashboard;
use App\Livewire\Reports\DocumentExports;
use App\Livewire\Reports\OperationsDashboard;
use App\Livewire\Verification\ReviewVerification;
use App\Livewire\Verification\VerificationQueue;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)->name('login');
});

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/mfa/challenge', MfaChallenge::class)
        ->name('mfa.challenge');

    Route::get('/dashboard', Dashboard::class)
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Document registration and retrieval
    |--------------------------------------------------------------------------
    */

    Route::get('/documents/register', RegisterDocument::class)
        ->name('documents.register');

    Route::get('/documents/search', SearchDocuments::class)
        ->name('documents.search');

    Route::get('/documents/{document}/viewer', DocumentViewer::class)
        ->name('documents.viewer');

    Route::get('/documents/{document}/upload', UploadPdf::class)
        ->name('documents.upload');

    Route::get('/documents/{document}/content', [
        DocumentFileController::class,
        'preview',
    ])->name('documents.file.preview');

    Route::get('/documents/{document}/download', [
        DocumentFileController::class,
        'download',
    ])->name('documents.file.download');

    Route::get('/documents/{document}', ShowDocument::class)
        ->name('documents.show');

    /*
    |--------------------------------------------------------------------------
    | PDF processing
    |--------------------------------------------------------------------------
    */

    Route::get('/processing', ProcessingDashboard::class)
        ->name('processing.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Verification
    |--------------------------------------------------------------------------
    */

    Route::get('/verification', VerificationQueue::class)
        ->name('verification.queue');

    Route::get('/verification/{verification}', ReviewVerification::class)
        ->name('verification.review');

    /*
    |--------------------------------------------------------------------------
    | Bulk migration
    |--------------------------------------------------------------------------
    */

    Route::get('/bulk/import', ImportMetadata::class)
        ->name('bulk.import');

    Route::get('/bulk/migrations/{batch}', MigrationBatchView::class)
        ->name('bulk.show');

    /*
    |--------------------------------------------------------------------------
    | Reporting
    |--------------------------------------------------------------------------
    */

    Route::get('/reports/dashboard', OperationsDashboard::class)
        ->name('reports.dashboard');

    Route::get('/reports/exports', DocumentExports::class)
        ->name('reports.exports');

    /*
    |--------------------------------------------------------------------------
    | Administration
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/audit', AuditLogViewer::class)
        ->name('admin.audit');

    Route::get('/admin/system-health', SystemHealth::class)
        ->name('admin.system-health');

    Route::get('/admin/retention', RetentionPolicies::class)
        ->name('admin.retention');
});

Route::middleware(['auth', 'active', 'mfa.recent'])->group(function (): void {
    Route::get('/admin/security-settings', SecuritySettings::class)
        ->name('admin.security-settings');
});
