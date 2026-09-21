<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WorkLog;
use App\Models\RoutineSchedule;
use App\Models\Category;

class Dashboard extends Component
{
    public function render()
    {
        $totalLogs = WorkLog::count();
        $inProgressLogs = WorkLog::where('status', 'in_progress')->count();
        $pendingLogs = WorkLog::whereIn('status', ['pending', 'waiting_sparepart', 'escalated'])->count();
        $completedLogs = WorkLog::where('status', 'completed')->count();
        $completedToday = WorkLog::where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();

        $recentLogs = WorkLog::with(['user', 'department', 'category', 'attachments'])
            ->latest('started_at')
            ->latest('id')
            ->take(6)
            ->get();

        $routines = RoutineSchedule::with('category')
            ->where('is_active', true)
            ->take(5)
            ->get();

        $categories = Category::withCount('workLogs')->get();

        return view('livewire.dashboard', [
            'totalLogs' => $totalLogs,
            'inProgressLogs' => $inProgressLogs,
            'pendingLogs' => $pendingLogs,
            'completedLogs' => $completedLogs,
            'completedToday' => $completedToday,
            'recentLogs' => $recentLogs,
            'routines' => $routines,
            'categories' => $categories,
        ]);
    }
}