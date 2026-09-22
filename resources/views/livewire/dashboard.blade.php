<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Dashboard Overview</h1>
            <p class="text-xs sm:text-sm opacity-60">Selamat datang kembali, <span class="font-semibold text-primary">{{ auth()->user()->name }}</span>! Pantau performa IT Support hari ini.</p>
        </div>
        <div class="w-full sm:w-auto">
            <x-mary-button label="Catat Pekerjaan Baru" icon="o-plus" link="{{ route('work-logs') }}" class="btn-primary shadow-lg w-full sm:w-auto" />
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <x-mary-stat 
            title="Total Tiket IT" 
            value="{{ $totalLogs }}" 
            icon="o-ticket" 
            description="Seluruh tiket terdaftar" 
            class="bg-base-100 shadow-md border border-base-content/5 text-primary p-3 sm:p-5 rounded-2xl" 
        />
        <x-mary-stat 
            title="Sedang Dikerjakan" 
            value="{{ $inProgressLogs }}" 
            icon="o-arrow-path" 
            description="Penanganan aktif" 
            class="bg-base-100 shadow-md border border-base-content/5 text-info p-3 sm:p-5 rounded-2xl" 
        />
        <x-mary-stat 
            title="Pending / Part" 
            value="{{ $pendingLogs }}" 
            icon="o-clock" 
            description="Menunggu part / eskalasi" 
            class="bg-base-100 shadow-md border border-base-content/5 text-warning p-3 sm:p-5 rounded-2xl" 
        />
        <x-mary-stat 
            title="Selesai (Done)" 
            value="{{ $completedLogs }}" 
            icon="o-check-circle" 
            description="{{ $completedToday }} selesai hari ini" 
            class="bg-base-100 shadow-md border border-base-content/5 text-success p-3 sm:p-5 rounded-2xl" 
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Worklog Terbaru -->
        <div class="lg:col-span-2 bg-base-100 p-4 sm:p-6 rounded-2xl shadow-md border border-base-content/5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <x-mary-icon name="o-document-text" class="text-primary w-5 h-5" />
                    Pekerjaan Terbaru
                </h2>
                <a href="{{ route('work-logs') }}" class="text-xs text-primary font-semibold hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr>
                            <th>Tiket & Judul</th>
                            <th>Pelapor / Dept</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                            <tr class="hover">
                                <td>
                                    <span class="badge badge-outline badge-xs font-mono font-bold text-primary">{{ $log->ticket_number }}</span>
                                    <div class="font-semibold text-sm line-clamp-1">{{ $log->title }}</div>
                                </td>
                                <td>
                                    <div class="text-xs font-medium">{{ $log->requester_name ?? 'Internal IT' }}</div>
                                    <span class="badge badge-ghost badge-xs">{{ $log->department->name ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($log->status === 'completed')
                                        <span class="badge badge-success badge-sm text-xs">Selesai</span>
                                    @elseif($log->status === 'in_progress')
                                        <span class="badge badge-info badge-sm text-xs">Proses</span>
                                    @elseif($log->status === 'waiting_sparepart')
                                        <span class="badge badge-warning badge-sm text-xs">Menunggu Part</span>
                                    @elseif($log->status === 'cancelled')
                                        <span class="badge badge-error badge-sm text-xs">Batal</span>
                                    @else
                                        <span class="badge badge-warning badge-sm text-xs">Pending</span>
                                    @endif
                                </td>
                                <td class="text-xs opacity-70">
                                    {{ $log->started_at ? $log->started_at->format('d M Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center opacity-50 py-6">Belum ada pekerjaan tercatat</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kolom Kanan: Jadwal Rutin & Kategori -->
        <div class="space-y-6">
            <!-- Jadwal Pemeliharaan Rutin -->
            <div class="bg-base-100 p-6 rounded-2xl shadow-md border border-base-content/5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold flex items-center gap-2">
                        <x-mary-icon name="o-calendar" class="text-secondary w-5 h-5" />
                        Jadwal Rutin
                    </h2>
                    <a href="{{ route('routine-schedules') }}" class="text-xs text-secondary font-semibold hover:underline">Semua &rarr;</a>
                </div>
                <div class="space-y-3">
                    @forelse($routines as $routine)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-base-200/50 border border-base-content/5">
                            <div>
                                <div class="font-medium text-sm">{{ $routine->title }}</div>
                                <div class="text-xs opacity-60">{{ $routine->category->name ?? '-' }}</div>
                            </div>
                            <span class="badge badge-sm badge-outline uppercase text-[10px] font-bold">{{ $routine->frequency }}</span>
                        </div>
                    @empty
                        <div class="text-center opacity-50 py-4 text-xs">Tidak ada jadwal aktif</div>
                    @endforelse
                </div>
            </div>

            <!-- Ringkasan Kategori -->
            <div class="bg-base-100 p-6 rounded-2xl shadow-md border border-base-content/5">
                <h2 class="text-base font-bold flex items-center gap-2 mb-4">
                    <x-mary-icon name="o-tag" class="text-accent w-5 h-5" />
                    Distribusi Kategori
                </h2>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <div class="flex items-center justify-between text-xs py-1.5 border-b border-base-content/5 last:border-0">
                            <span class="font-medium">{{ $cat->name }} ({{ $cat->code }})</span>
                            <span class="badge badge-ghost badge-sm">{{ $cat->work_logs_count }} tiket</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>