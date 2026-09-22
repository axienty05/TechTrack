<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkLog;
use Carbon\Carbon;

class WorkLogPrintController extends Controller
{
    public function print(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type', 'month'); // 'month' or 'range'

        $query = WorkLog::with(['department', 'category'])
            ->where('user_id', $user->id);

        if ($type === 'month') {
            $month = $request->query('month', date('Y-m'));
            $start = Carbon::parse($month . '-01')->startOfMonth();
            $end = Carbon::parse($month . '-01')->endOfMonth();
            $periodLabel = $start->translatedFormat('F Y');
        } else {
            $startDate = $request->query('start_date', date('Y-m-01'));
            $endDate = $request->query('end_date', date('Y-m-d'));
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $periodLabel = $start->translatedFormat('d M Y') . ' s/d ' . $end->translatedFormat('d M Y');
        }

        $workLogs = $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('started_at', [$start, $end])
              ->orWhere(function ($sub) use ($start, $end) {
                  $sub->whereNull('started_at')
                      ->whereBetween('created_at', [$start, $end]);
              });
        })
        ->orderBy('started_at', 'asc')
        ->get();

        $stats = [
            'total'       => $workLogs->count(),
            'completed'   => $workLogs->where('status', 'completed')->count(),
            'in_progress' => $workLogs->whereIn('status', ['in_progress', 'pending', 'waiting_sparepart'])->count(),
            'preventive'  => $workLogs->where('task_type', 'preventive')->count(),
            'reactive'    => $workLogs->where('task_type', 'reactive')->count(),
        ];

        return view('work-logs.print', compact('workLogs', 'user', 'periodLabel', 'stats', 'type', 'start', 'end'));
    }
}
