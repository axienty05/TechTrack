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

Route::get('/login', Login::class)->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/work-logs', WorkLogsIndex::class)->name('work-logs');
    Route::get('/routine-schedules', RoutineSchedulesIndex::class)->name('routine-schedules');
    Route::get('/pc-maintenance', PcMaintenanceIndex::class)->name('pc-maintenance');
    Route::get('/profile', ProfileEdit::class)->name('profile');

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