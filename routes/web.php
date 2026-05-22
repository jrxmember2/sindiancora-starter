<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordSetupController;
use App\Http\Controllers\CompanySwitchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\CondominiumGovernanceController;
use App\Http\Controllers\SuperAdmin\LicenseController;
use App\Http\Controllers\SuperAdmin\ModuleController;
use App\Http\Controllers\SuperAdmin\PlatformUserController;
use App\Http\Controllers\SuperAdmin\VersionController;
use App\Http\Controllers\Tenant\CondominiumLinkRequestController;
use App\Http\Controllers\Tenant\CondominiumController;
use App\Http\Controllers\Tenant\DocumentController;
use App\Http\Controllers\Tenant\IssueController;
use App\Http\Controllers\Tenant\LicenseOverviewController;
use App\Http\Controllers\Tenant\SupplierController;
use App\Http\Controllers\Tenant\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/primeiro-acesso', [PasswordSetupController::class, 'edit'])->name('password.setup.edit');
    Route::put('/primeiro-acesso', [PasswordSetupController::class, 'update'])->name('password.setup.update');

    Route::middleware('password.changed')->group(function () {
        Route::post('/trocar-empresa', CompanySwitchController::class)->name('companies.switch');
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::prefix('superadmin')->name('superadmin.')->middleware('superadmin')->group(function () {
            Route::resource('companies', CompanyController::class)->except(['show']);
            Route::resource('licenses', LicenseController::class)->except(['show']);
            Route::resource('modules', ModuleController::class)->only(['index']);
            Route::resource('platform-users', PlatformUserController::class)->except(['show']);
            Route::get('condominium-governance', [CondominiumGovernanceController::class, 'index'])->name('condominium-governance.index');
            Route::post('condominium-governance/{condominium}/force-transfer', [CondominiumGovernanceController::class, 'forceTransfer'])->name('condominium-governance.force-transfer');
            Route::get('versions', VersionController::class)->name('versions.index');
        });

        Route::prefix('app')->middleware(['company.selected'])->group(function () {
            Route::get('license', LicenseOverviewController::class)->name('tenant.license.show');
            Route::get('condominium-link-requests', [CondominiumLinkRequestController::class, 'index'])->name('tenant.condominium-links.index');
            Route::post('condominium-link-requests/{condominiumLinkRequest}/decide', [CondominiumLinkRequestController::class, 'decide'])->name('tenant.condominium-links.decide');

            Route::middleware(['license.active'])->group(function () {
                Route::resource('users', UserController::class)
                    ->parameters(['users' => 'companyUser'])
                    ->middleware('module:configuracoes');
                Route::resource('condominiums', CondominiumController::class)->middleware('module:configuracoes');
                Route::resource('suppliers', SupplierController::class)->middleware('module:fornecedores');
                Route::resource('issues', IssueController::class)->middleware('module:chamados');
                Route::resource('documents', DocumentController::class)->middleware('module:documentos');
            });
        });
    });
});
