<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\WorkLogs\Index as WorkLogsIndex;
use App\Livewire\RoutineSchedules\Index as RoutineSchedulesIndex;
use App\Livewire\Departments\Index as DepartmentsIndex;
use App\Livewire\Categories\Index as CategoriesIndex;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Profile\Edit as ProfileEdit;
use App\Livewire\PcMaintenance\Index as PcMaintenanceIndex;
use App\Http\Controllers\WorkLogPrintController;

Route::get('/login', Login::class)->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/work-logs', WorkLogsIndex::class)->name('work-logs');
    Route::get('/work-logs/print', [WorkLogPrintController::class, 'print'])->name('work-logs.print');
    Route::get('/routine-schedules', RoutineSchedulesIndex::class)->name('routine-schedules');
    Route::get('/pc-maintenance', PcMaintenanceIndex::class)->name('pc-maintenance');
    Route::get('/profile', ProfileEdit::class)->name('profile');

    // ─── INVENTARIS & ASET IT ───
    Route::get('/inventaris/pemakai', \App\Livewire\Pemakais\Index::class)->name('pemakais');
    Route::get('/inventaris/barang', \App\Livewire\Barangs\Index::class)->name('barangs');
    Route::get('/inventaris/barang/tambah', \App\Livewire\Barangs\Form::class)->name('barangs.create');
    Route::get('/inventaris/barang/{barang}/edit', \App\Livewire\Barangs\Form::class)->name('barangs.edit');
    Route::get('/inventaris/mutasi', \App\Livewire\Mutasis\Index::class)->name('mutasis');
    Route::get('/inventaris/mutasi/tambah', \App\Livewire\Mutasis\Form::class)->name('mutasis.create');
    Route::get('/inventaris/supplier', \App\Livewire\Suppliers\Index::class)->name('suppliers');
    Route::get('/inventaris/service-center', \App\Livewire\ServiceCenters\Index::class)->name('service-centers');
    Route::get('/inventaris/service', \App\Livewire\Services\Index::class)->name('services');
    Route::get('/inventaris/service-internal', \App\Livewire\ServiceInternals\Index::class)->name('service-internals');
    Route::get('/inventaris/service-internal/print', [\App\Http\Controllers\ServiceInternalPrintController::class, 'print'])->name('service-internals.print');

    // Master Data - hanya bisa diakses oleh admin dan it_lead
    Route::middleware('role:admin,it_lead')->group(function () {
        Route::get('/departments', DepartmentsIndex::class)->name('departments');
        Route::get('/categories', CategoriesIndex::class)->name('categories');
        Route::get('/users', UsersIndex::class)->name('users');
    });

    Route::match(['get', 'post'], '/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});