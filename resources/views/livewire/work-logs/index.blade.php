<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Manajemen Worklog IT</h1>
            <p class="text-xs sm:text-sm opacity-60">Catat, pantau, dan kelola seluruh penanganan insiden dan pemeliharaan IT</p>
        </div>
        <div class="w-full sm:w-auto">
            <x-mary-button label="Tambah Worklog Baru" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg w-full sm:w-auto" />
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-base-100 p-3 sm:p-4 rounded-2xl shadow-md border border-base-content/5 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 sm:gap-3">
            <div class="sm:col-span-2">
                <x-mary-input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari no. tiket, judul, pelapor, device..." 
                    icon="o-magnifying-glass" 
                    clearable 
                />
            </div>
            <div>
                <select wire:model.live="statusFilter" class="select select-bordered select-sm w-full">
                    <option value="">Semua Status</option>
                    <option value="in_progress">In Progress</option>
                    <option value="pending">Pending</option>
                    <option value="waiting_sparepart">Waiting Sparepart</option>
                    <option value="escalated">Escalated</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <select wire:model.live="categoryFilter" class="select select-bordered select-sm w-full">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="departmentFilter" class="select select-bordered select-sm w-full">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 2: Filter Tanggal & Presets & Prioritas -->
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3 pt-3 border-t border-base-content/5">
            <!-- Left: Date Filter & Presets -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 w-full lg:w-auto">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-base-content/80">
                    <x-mary-icon name="o-calendar" class="w-4 h-4 text-primary flex-shrink-0" />
                    <span>Filter Tanggal:</span>
                </div>

                <!-- Presets Button Group (Scrollable on small mobile) -->
                <div class="overflow-x-auto pb-1 sm:pb-0 scrollbar-none w-full sm:w-auto">
                    <div class="join inline-flex min-w-max">
                        <button type="button" wire:click="setDatePreset('all')"
                            class="btn btn-xs join-item {{ empty($datePreset) && empty($startDate) && empty($endDate) ? 'btn-primary' : 'btn-ghost' }}">
                            Semua
                        </button>
                        <button type="button" wire:click="setDatePreset('today')"
                            class="btn btn-xs join-item {{ $datePreset === 'today' ? 'btn-primary' : 'btn-ghost' }}">
                            Hari Ini
                        </button>
                        <button type="button" wire:click="setDatePreset('this_week')"
                            class="btn btn-xs join-item {{ $datePreset === 'this_week' ? 'btn-primary' : 'btn-ghost' }}">
                            Minggu Ini
                        </button>
                        <button type="button" wire:click="setDatePreset('this_month')"
                            class="btn btn-xs join-item {{ $datePreset === 'this_month' ? 'btn-primary' : 'btn-ghost' }}">
                            Bulan Ini
                        </button>
                    </div>
                </div>

                <div class="h-4 w-[1px] bg-base-content/10 hidden sm:block"></div>

                <!-- Custom Range Date Inputs -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <div class="flex items-center gap-1.5 flex-1 sm:flex-initial">
                        <span class="text-xs opacity-60 font-medium">Dari:</span>
                        <input type="date" wire:model.live="startDate"
                            class="input input-bordered input-xs h-8 text-xs rounded-lg w-full sm:w-auto" />
                    </div>
                    <span class="text-xs opacity-40">-</span>
                    <div class="flex items-center gap-1.5 flex-1 sm:flex-initial">
                        <span class="text-xs opacity-60 font-medium">Sampai:</span>
                        <input type="date" wire:model.live="endDate"
                            class="input input-bordered input-xs h-8 text-xs rounded-lg w-full sm:w-auto" />
                    </div>
                </div>
            </div>

            <!-- Right: Priority Filter & Reset Button -->
            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                <select wire:model.live="priorityFilter" class="select select-bordered select-xs h-8 text-xs rounded-lg flex-1 sm:flex-initial">
                    <option value="">Semua Prioritas</option>
                    <option value="critical">🔴 Critical</option>
                    <option value="high">🟠 High</option>
                    <option value="medium">🟡 Medium</option>
                    <option value="low">🟢 Low</option>
                </select>

                @if($search || $statusFilter || $categoryFilter || $departmentFilter || $priorityFilter || $startDate || $endDate || $datePreset)
                    <button type="button" wire:click="resetFilters" class="btn btn-ghost btn-xs h-8 text-error gap-1 rounded-lg flex-shrink-0" title="Reset Semua Filter">
                        <x-mary-icon name="o-x-mark" class="w-3.5 h-3.5" />
                        <span>Reset</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Worklog Table Card -->
    <div class="bg-base-100 rounded-2xl shadow-md border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-sm sm:table-md w-full">
                <thead class="bg-base-200/50">
                    <tr>
                        <th class="whitespace-nowrap">No. Tiket & Tanggal</th>
                        <th>Pekerjaan & Pelapor</th>
                        <th class="whitespace-nowrap">Kategori & Divisi</th>
                        <th class="whitespace-nowrap">Status & Prioritas</th>
                        <th class="whitespace-nowrap">Lampiran Foto</th>
                        <th class="text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workLogs as $log)
                        <tr class="hover">
                            <!-- No Tiket & Tanggal -->
                            <td>
                                <div class="font-mono font-bold text-primary text-xs">{{ $log->ticket_number }}</div>
                                <div class="text-xs opacity-60 mt-0.5">
                                    {{ $log->started_at ? $log->started_at->format('d M Y') : '-' }}
                                </div>
                                <span class="badge badge-outline badge-xs mt-1 uppercase">{{ $log->task_type }}</span>
                            </td>

                            <!-- Pekerjaan & Pelapor -->
                            <td>
                                <div class="font-bold text-sm text-base-content hover:text-primary cursor-pointer" wire:click="openDetailModal({{ $log->id }})">
                                    {{ $log->title }}
                                </div>
                                <div class="text-xs opacity-70 mt-0.5">
                                    Pelapor: <span class="font-medium">{{ $log->requester_name ?? 'Internal IT' }}</span>
                                    @if($log->device_identifier)
                                        <span class="opacity-50">({{ $log->device_identifier }})</span>
                                    @endif
                                </div>
                                @if($log->description)
                                    <div class="text-xs opacity-50 line-clamp-1 max-w-xs mt-1">{{ $log->description }}</div>
                                @endif
                            </td>

                            <!-- Kategori & Divisi -->
                            <td>
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="badge badge-sm font-medium whitespace-nowrap" style="background-color: {{ $log->category->color_hex ?? '#3b82f6' }}20; color: {{ $log->category->color_hex ?? '#3b82f6' }}; border-color: {{ $log->category->color_hex ?? '#3b82f6' }}40;">
                                        {{ $log->category->name ?? '-' }}
                                    </span>
                                    <span class="badge badge-ghost badge-sm text-xs">
                                        {{ $log->department->name ?? '-' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Status & Prioritas -->
                            <td>
                                <div class="flex flex-col gap-1 items-start">
                                    @if($log->status === 'completed')
                                        <span class="badge badge-success badge-sm text-xs">Selesai</span>
                                    @elseif($log->status === 'in_progress')
                                        <span class="badge badge-info badge-sm text-xs">Proses</span>
                                    @elseif($log->status === 'waiting_sparepart')
                                        <span class="badge badge-warning badge-sm text-xs">Menunggu Part</span>
                                    @elseif($log->status === 'escalated')
                                        <span class="badge badge-secondary badge-sm text-xs">Eskalasi</span>
                                    @elseif($log->status === 'cancelled')
                                        <span class="badge badge-error badge-sm text-xs">Batal</span>
                                    @else
                                        <span class="badge badge-warning badge-sm text-xs">Pending</span>
                                    @endif

                                    @if($log->priority === 'critical')
                                        <span class="badge badge-error badge-xs">Critical</span>
                                    @elseif($log->priority === 'high')
                                        <span class="badge badge-warning badge-xs">High</span>
                                    @elseif($log->priority === 'medium')
                                        <span class="badge badge-ghost badge-xs">Medium</span>
                                    @else
                                        <span class="badge badge-ghost badge-xs opacity-60">Low</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Lampiran Foto (Thumbnail + Modal Pop-up + Direct Link) -->
                            <td>
                                @if($log->attachments->count() > 0)
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @foreach($log->attachments->take(3) as $att)
                                            <div class="relative group cursor-pointer" 
                                                 wire:click="openImagePreview('{{ asset('storage/' . $att->file_path) }}', '{{ $log->ticket_number }} - {{ $log->title }}', '{{ $att->caption }}', '{{ $att->attachment_type }}')"
                                                 title="Klik untuk melihat foto pop-up">
                                                <img src="{{ asset('storage/' . $att->file_path) }}" 
                                                     alt="lampiran" 
                                                     class="w-10 h-10 object-cover rounded-lg border border-base-content/20 shadow-sm transition-transform group-hover:scale-110" />
                                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 rounded-lg flex items-center justify-center transition-opacity">
                                                    <x-mary-icon name="o-magnifying-glass-plus" class="w-4 h-4 text-white" />
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($log->attachments->count() > 3)
                                            <span class="badge badge-neutral badge-xs">+{{ $log->attachments->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs opacity-40 italic">Tidak ada</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openDetailModal({{ $log->id }})" class="btn btn-ghost btn-xs btn-square" title="Detail Tiket">
                                        <x-mary-icon name="o-eye" class="w-4 h-4 text-info" />
                                    </button>
                                    <button wire:click="openEditModal({{ $log->id }})" class="btn btn-ghost btn-xs btn-square" title="Edit Pekerjaan">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4 text-warning" />
                                    </button>
                                    <button wire:click="delete({{ $log->id }})" wire:confirm="Yakin ingin menghapus worklog ini?" class="btn btn-ghost btn-xs btn-square text-error" title="Hapus">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 opacity-50">
                                <x-mary-icon name="o-document-magnifying-glass" class="w-10 h-10 mx-auto mb-2 opacity-40" />
                                Tidak ada pekerjaan yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $workLogs->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 1: POP-UP IMAGE PREVIEW                  -->
    <!-- ============================================== -->
    <x-mary-modal wire:model="showImageModal" class="backdrop-blur-sm" box-class="max-w-4xl p-3 sm:p-5 w-full max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-base-content/10 mb-4">
            <div class="flex items-center gap-2">
                <x-mary-icon name="o-photo" class="w-6 h-6 text-primary flex-shrink-0" />
                <div>
                    <h3 class="font-bold text-base sm:text-lg leading-tight">{{ $previewImage['title'] }}</h3>
                    <div class="text-xs opacity-60 mt-0.5">Tipe Bukti: <span class="badge badge-outline badge-xs uppercase font-bold">{{ $previewImage['type'] }}</span></div>
                </div>
            </div>
            
        </div>

        @if($previewImage['url'])
            <div class="relative bg-base-300 rounded-xl p-2 flex items-center justify-center overflow-hidden min-h-[220px] max-h-[70vh]">
                <img src="{{ $previewImage['url'] }}" 
                     alt="Preview Foto" 
                     class="max-h-[65vh] w-auto max-w-full object-contain rounded-lg shadow-xl" />
            </div>
        @endif

        @if(!empty($previewImage['caption']))
            <div class="mt-4 p-3 bg-base-200 rounded-xl border border-base-content/10 text-sm">
                <span class="font-semibold text-xs opacity-60 uppercase tracking-wider block mb-1">Keterangan / Catatan Foto:</span>
                <p class="italic text-base-content">{{ $previewImage['caption'] }}</p>
            </div>
        @endif

        <div class="mt-6 flex flex-col-reverse sm:flex-row items-center justify-between gap-2 pt-3 border-t border-base-content/10">
            <a href="{{ $previewImage['url'] }}" target="_blank" class="btn btn-outline btn-sm gap-2 w-full sm:w-auto">
                <x-mary-icon name="o-arrow-top-right-on-square" class="w-4 h-4" />
                Buka Gambar di Tab Baru
            </a>
            <button wire:click="$set('showImageModal', false)" class="btn btn-sm btn-ghost w-full sm:w-auto">
                Tutup
            </button>
        </div>
    </x-mary-modal>

    <!-- ============================================== -->
    <!-- MODAL 2: FORM CREATE / EDIT WORKLOG            -->
    <!-- ============================================== -->
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-3xl p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-base-content/10 mb-4 gap-2">
            <h3 class="font-bold text-base sm:text-lg flex items-center gap-2 truncate">
                <x-mary-icon name="o-document-text" class="text-primary flex-shrink-0" />
                <span class="truncate">{{ $workLogId ? 'Edit Worklog Tiket' : 'Catat Pekerjaan IT Baru' }}</span>
            </h3>
            <span class="badge badge-primary badge-outline font-mono font-bold text-xs flex-shrink-0">{{ $ticket_number }}</span>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Judul Pekerjaan / Permasalahan *</label>
                    <x-mary-input wire:model.live.debounce.300ms="title" placeholder="Contoh: Perbaikan PC kasir mati total, Maintenance PC Lama Gbaku..." />
                    @if($detectedDevSummary)
                        <div class="mt-2 p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-between text-xs text-emerald-600 dark:text-emerald-400">
                            <div class="flex items-center gap-2">
                                <x-mary-icon name="o-check-badge" class="w-4 h-4 flex-shrink-0 text-emerald-500" />
                                <span>
                                    Terhubung Otomatis ke PC Maintenance: <strong>{{ $detectedDevSummary }}</strong>
                                </span>
                            </div>
                            <span class="badge badge-sm {{ $detectedDevLocation === 'pabrik' ? 'badge-warning' : 'badge-info' }} font-semibold text-[10px] uppercase">
                                Unit {{ $detectedDevLocation === 'pabrik' ? 'Pabrik' : 'Kantor' }}
                            </span>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Kategori Pekerjaan *</label>
                    <select wire:model="category_id" class="select select-bordered w-full">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Departemen Pemohon</label>
                    <select wire:model="department_id" class="select select-bordered w-full">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Nama Pemohon / Pelapor</label>
                    <x-mary-input wire:model.live.debounce.300ms="requester_name" placeholder="Nama staf / pemohon" />
                </div>

                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">ID Perangkat (Device Identifier)</label>
                    <x-mary-input wire:model.live.debounce.300ms="device_identifier" placeholder="Contoh: SC, RMWH, PC-FA-01..." />
                </div>

                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Tipe Tugas</label>
                    <select wire:model="task_type" class="select select-bordered w-full">
                        <option value="reactive">Reaktif (Insidental / Laporan)</option>
                        <option value="preventive">Preventif (Pemeliharaan Rutin)</option>
                        <option value="administrative">Administratif (Support)</option>
                    </select>
                </div>

                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Prioritas</label>
                    <select wire:model="priority" class="select select-bordered w-full">
                        <option value="low">Low (Rendah)</option>
                        <option value="medium">Medium (Sedang)</option>
                        <option value="high">High (Tinggi)</option>
                        <option value="critical">Critical (Kritis)</option>
                    </select>
                </div>

                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Status Penanganan *</label>
                    <select wire:model="status" class="select select-bordered w-full">
                        <option value="in_progress">In Progress (Sedang Dikerjakan)</option>
                        <option value="pending">Pending (Menunggu)</option>
                        <option value="waiting_sparepart">Waiting Sparepart (Menunggu Part)</option>
                        <option value="escalated">Escalated (Eskalasi ke Vendor/Lead)</option>
                        <option value="completed">Completed (Selesai)</option>
                        <option value="cancelled">Cancelled (Dibatalkan)</option>
                    </select>
                </div>

                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Tanggal Mulai Pengerjaan *</label>
                    <input type="date" wire:model="started_at" class="input input-bordered w-full" />
                </div>

                <div>
                    <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Tanggal Selesai (Opsional)</label>
                    <input type="date" wire:model="completed_at" class="input input-bordered w-full" />
                </div>
            </div>

            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Deskripsi Kendala / Masalah</label>
                <textarea wire:model="description" rows="2" class="textarea textarea-bordered w-full" placeholder="Jelaskan kendala teknis atau keluhan awal pemohon..."></textarea>
            </div>

            <div>
                <label class="label text-xs font-bold uppercase tracking-wider opacity-70">Tindakan Perbaikan yang Dilakukan</label>
                <textarea wire:model="action_taken" rows="2" class="textarea textarea-bordered w-full" placeholder="Jelaskan langkah-langkah penanganan/solusi yang telah diambil..."></textarea>
            </div>

            <!-- Existing Attachments (When editing) -->
            @if(!empty($existingAttachments))
                <div class="p-3 bg-base-200/50 rounded-xl border border-base-content/5 space-y-2">
                    <span class="font-bold text-xs uppercase tracking-wider opacity-70 block">Foto Terlampir Saat Ini:</span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($existingAttachments as $att)
                            <div class="relative group bg-base-100 p-1.5 rounded-lg border border-base-content/10">
                                <img src="{{ $att['url'] }}" alt="att" class="w-full h-20 object-cover rounded" />
                                <span class="badge badge-neutral badge-xs uppercase mt-1 block">{{ $att['type'] }}</span>
                                <button type="button" 
                                        wire:click="deleteAttachment({{ $att['id'] }})" 
                                        wire:confirm="Hapus foto lampiran ini?"
                                        class="btn btn-circle btn-error btn-xs absolute top-2 right-2 opacity-80 hover:opacity-100"><x-mary-icon name="o-trash" class="w-3.5 h-3.5" /></button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upload Foto Bukti (Bisa Langsung Upload 2 Foto: Before & After Sekaligus) -->
            <div class="p-4 bg-base-200/40 rounded-2xl border border-base-content/10 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-xs uppercase tracking-wider opacity-80 flex items-center gap-2">
                        <x-mary-icon name="o-camera" class="w-4 h-4 text-primary" />
                        Foto Bukti / Lampiran (Bisa Langsung Upload 2 Foto Sekaligus)
                    </span>
                    <span class="text-[11px] opacity-50">PNG, JPG, JPEG (Maks. 10MB)</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Slot 1: Foto Sebelum Perbaikan (Before) -->
                    <div class="p-3 bg-base-100 rounded-xl border border-base-content/10 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                1. Foto Sebelum (Before)
                            </span>
                            @if($beforePhoto)
                                <button type="button" wire:click="$set('beforePhoto', null)" class="btn btn-ghost btn-xs text-error gap-1 px-1.5 h-6 min-h-0">
                                    <x-mary-icon name="o-x-mark" class="w-3.5 h-3.5" /> Batal
                                </button>
                            @endif
                        </div>

                        @if($beforePhoto && method_exists($beforePhoto, 'temporaryUrl'))
                            <div class="relative overflow-hidden rounded-lg">
                                <img src="{{ $beforePhoto->temporaryUrl() }}" alt="Before preview" class="w-full h-28 object-cover rounded-lg border border-base-content/10" />
                                <div class="text-[10px] text-success font-medium mt-1 flex items-center gap-1">
                                    <x-mary-icon name="o-check" class="w-3 h-3" /> Siap diunggah
                                </div>
                            </div>
                        @endif

                        <div>
                            <input type="file" wire:model="beforePhoto" accept="image/*" class="file-input file-input-bordered file-input-xs w-full" />
                        </div>
                        <div>
                            <input type="text" wire:model="beforeCaption" placeholder="Catatan foto sebelum (opsional)..." class="input input-bordered input-xs w-full text-xs" />
                        </div>
                    </div>

                    <!-- Slot 2: Foto Sesudah Perbaikan (After) -->
                    <div class="p-3 bg-base-100 rounded-xl border border-base-content/10 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                2. Foto Sesudah (After)
                            </span>
                            @if($afterPhoto)
                                <button type="button" wire:click="$set('afterPhoto', null)" class="btn btn-ghost btn-xs text-error gap-1 px-1.5 h-6 min-h-0">
                                    <x-mary-icon name="o-x-mark" class="w-3.5 h-3.5" /> Batal
                                </button>
                            @endif
                        </div>

                        @if($afterPhoto && method_exists($afterPhoto, 'temporaryUrl'))
                            <div class="relative overflow-hidden rounded-lg">
                                <img src="{{ $afterPhoto->temporaryUrl() }}" alt="After preview" class="w-full h-28 object-cover rounded-lg border border-base-content/10" />
                                <div class="text-[10px] text-success font-medium mt-1 flex items-center gap-1">
                                    <x-mary-icon name="o-check" class="w-3 h-3" /> Siap diunggah
                                </div>
                            </div>
                        @endif

                        <div>
                            <input type="file" wire:model="afterPhoto" accept="image/*" class="file-input file-input-bordered file-input-xs w-full" />
                        </div>
                        <div>
                            <input type="text" wire:model="afterCaption" placeholder="Catatan foto sesudah (opsional)..." class="input input-bordered input-xs w-full text-xs" />
                        </div>
                    </div>
                </div>

                <!-- Foto Tambahan / Screenshot Lainnya (Opsional) -->
                <details class="collapse collapse-arrow bg-base-100 rounded-xl border border-base-content/10">
                    <summary class="collapse-title text-xs font-semibold py-2 min-h-0 opacity-70">
                        + Tambah Foto Lain / Screenshot Error (Opsional)
                    </summary>
                    <div class="collapse-content pt-2 space-y-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div>
                                <label class="text-[11px] opacity-60 block mb-1">Tipe Lampiran</label>
                                <select wire:model="newAttachmentType" class="select select-bordered select-xs w-full">
                                    <option value="error_screenshot">Tangkapan Layar Error</option>
                                    <option value="scan_document">Dokumen Scan / Bukti Lain</option>
                                    <option value="before">Foto Sebelum (Before)</option>
                                    <option value="after">Foto Sesudah (After)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[11px] opacity-60 block mb-1">Keterangan Tambahan</label>
                                <input type="text" wire:model="newAttachmentCaption" placeholder="Catatan singkat foto..." class="input input-bordered input-xs w-full text-xs" />
                            </div>
                        </div>
                        <input type="file" wire:model="newAttachments" multiple accept="image/*" class="file-input file-input-bordered file-input-xs w-full" />
                    </div>
                </details>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md w-full sm:w-auto" wire:loading.attr="disabled">
                    <span wire:loading class="loading loading-spinner loading-xs"></span>
                    Simpan Worklog
                </button>
            </div>
        </form>
    </x-mary-modal>

    <!-- ============================================== -->
    <!-- MODAL 3: DETAIL TIKET LENGKAP                  -->
    <!-- ============================================== -->
    <x-mary-modal wire:model="showDetailModal" class="backdrop-blur-sm" box-class="max-w-3xl p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        @if($detailLog)
            <div class="flex items-center justify-between pb-3 border-b border-base-content/10 mb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="badge badge-primary font-mono font-bold">{{ $detailLog->ticket_number }}</span>
                        <span class="badge badge-outline uppercase text-xs">{{ $detailLog->task_type }}</span>
                    </div>
                    <h3 class="font-extrabold text-lg sm:text-xl mt-1">{{ $detailLog->title }}</h3>
                </div>
                
            </div>

            <div class="space-y-4 text-sm">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3 bg-base-200 rounded-xl">
                    <div>
                        <span class="text-xs opacity-60 block">Pelapor</span>
                        <span class="font-bold">{{ $detailLog->requester_name ?? 'Internal IT' }}</span>
                    </div>
                    <div>
                        <span class="text-xs opacity-60 block">Departemen</span>
                        <span class="font-bold">{{ $detailLog->department->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs opacity-60 block">Kategori</span>
                        <span class="font-bold">{{ $detailLog->category->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs opacity-60 block">Device</span>
                        <span class="font-bold">{{ $detailLog->device_identifier ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs opacity-60 block">Prioritas</span>
                        <span class="font-bold uppercase text-xs">{{ $detailLog->priority }}</span>
                    </div>
                    <div>
                        <span class="text-xs opacity-60 block">Status</span>
                        <span class="font-bold uppercase text-xs text-primary">{{ $detailLog->status }}</span>
                    </div>
                    <div>
                        <span class="text-xs opacity-60 block">Tanggal Mulai</span>
                        <span class="font-medium text-xs">{{ $detailLog->started_at ? $detailLog->started_at->format('d M Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs opacity-60 block">Tanggal Selesai</span>
                        <span class="font-medium text-xs">{{ $detailLog->completed_at ? $detailLog->completed_at->format('d M Y') : '-' }}</span>
                    </div>
                </div>

                @if($detailLog->description)
                    <div>
                        <h4 class="font-bold text-xs uppercase opacity-70 mb-1">Deskripsi Permasalahan:</h4>
                        <div class="p-3 bg-base-100 rounded-xl border border-base-content/10 whitespace-pre-wrap">{{ $detailLog->description }}</div>
                    </div>
                @endif

                @if($detailLog->action_taken)
                    <div>
                        <h4 class="font-bold text-xs uppercase opacity-70 mb-1">Tindakan Solusi / Penanganan:</h4>
                        <div class="p-3 bg-base-100 rounded-xl border border-base-content/10 whitespace-pre-wrap">{{ $detailLog->action_taken }}</div>
                    </div>
                @endif

                @if($detailLog->attachments->count() > 0)
                    <div>
                        <h4 class="font-bold text-xs uppercase opacity-70 mb-2">Galeri Foto Bukti ({{ $detailLog->attachments->count() }}):</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($detailLog->attachments as $att)
                                <div class="bg-base-100 p-2 rounded-xl border border-base-content/10 space-y-1.5 cursor-pointer group"
                                     wire:click="openImagePreview('{{ asset('storage/' . $att->file_path) }}', '{{ $detailLog->ticket_number }}', '{{ $att->caption }}', '{{ $att->attachment_type }}')">
                                    <div class="relative overflow-hidden rounded-lg">
                                        <img src="{{ asset('storage/' . $att->file_path) }}" alt="att" class="w-full h-28 object-cover rounded-lg group-hover:scale-105 transition-transform" />
                                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                            <x-mary-icon name="o-magnifying-glass-plus" class="w-6 h-6 text-white" />
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="badge badge-neutral badge-xs uppercase font-bold">{{ $att->attachment_type }}</span>
                                    </div>
                                    @if($att->caption)
                                        <p class="text-xs opacity-70 line-clamp-2 italic">{{ $att->caption }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2 pt-3 border-t border-base-content/10">
                <button wire:click="$set('showDetailModal', false)" class="btn btn-ghost btn-sm w-full sm:w-auto">Tutup</button>
                <button wire:click="openEditModal({{ $detailLog->id }})" class="btn btn-warning btn-sm gap-2 w-full sm:w-auto">
                    <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                    Edit Worklog
                </button>
            </div>
        @endif
    </x-mary-modal>
</div>