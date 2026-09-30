<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Service Eksternal</h1>
            <p class="text-xs sm:text-sm opacity-60">Pencatatan perbaikan barang IT ke vendor/service center luar</p>
        </div>
        <x-mary-button label="Catat Service Baru" icon="o-plus" wire:click="openCreateModal" class="btn-primary shadow-lg w-full sm:w-auto" />
    </div>

    {{-- Filters --}}
    <div class="bg-base-100 p-4 rounded-2xl shadow-md border border-base-content/5 flex flex-wrap gap-4 items-end">
        <div class="w-full sm:w-72">
            <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari no. SJ, barang, pemakai, vendor..." icon="o-magnifying-glass" clearable />
        </div>
        <div class="w-full sm:w-44">
            <select wire:model.live="filterStatus" class="select select-bordered select-sm w-full">
                <option value="">Semua Status</option>
                <option value="proses">Sedang Diproses</option>
                <option value="selesai">Selesai</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-base-100 rounded-2xl shadow-md border border-base-content/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead class="bg-base-200/50">
                    <tr>
                        <th>No. SJ</th>
                        <th>Barang</th>
                        <th>Pemakai</th>
                        <th>Service Center</th>
                        <th>Tgl Kirim</th>
                        <th>Tgl Selesai</th>
                        <th>Biaya</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $s)
                        <tr class="hover">
                            <td class="font-mono text-xs font-bold">{{ $s->no_sj ?? '-' }}</td>
                            <td>
                                <div class="font-bold text-sm">{{ $s->barang?->nama_barang }}</div>
                                <div class="font-mono text-xs opacity-50">{{ $s->barang?->kode_barang }}</div>
                            </td>
                            <td>
                                <div class="text-sm">{{ $s->pemakai?->nama ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="text-sm font-medium">{{ $s->serviceCenter?->nama_service }}</div>
                                @if($s->serviceCenter?->no_telp)
                                    <div class="text-xs opacity-50 font-mono">{{ $s->serviceCenter->no_telp }}</div>
                                @endif
                            </td>
                            <td class="text-sm font-mono">{{ $s->tgl_service ? $s->tgl_service->format('d/m/Y') : '-' }}</td>
                            <td class="text-sm font-mono">{{ $s->tgl_selesai ? $s->tgl_selesai->format('d/m/Y') : '-' }}</td>
                            <td class="text-sm font-mono">
                                {{ $s->biaya ? 'Rp ' . number_format($s->biaya, 0, ',', '.') : '-' }}
                            </td>
                            <td>
                                @if($s->tgl_selesai)
                                    <span class="badge badge-success badge-sm gap-1">
                                        <x-mary-icon name="o-check-circle" class="w-3 h-3" /> Selesai
                                    </span>
                                @else
                                    <span class="badge badge-warning badge-sm gap-1">
                                        <x-mary-icon name="o-clock" class="w-3 h-3" /> Dalam Proses
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $s->id }})" class="btn btn-ghost btn-xs btn-square" title="Edit">
                                        <x-mary-icon name="o-pencil-square" class="w-4 h-4 text-warning" />
                                    </button>
                                    <button wire:click="delete({{ $s->id }})" wire:confirm="Hapus data service ini?" class="btn btn-ghost btn-xs btn-square text-error" title="Hapus">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 opacity-50">Belum ada data service eksternal</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-base-content/10 bg-base-200/30">
            {{ $services->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    {{-- Modal Form Service --}}
    <x-mary-modal wire:model="showModal" class="backdrop-blur-sm" box-class="max-w-2xl p-4 sm:p-6 w-full max-h-[92vh] overflow-y-auto">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
            <x-mary-icon name="o-wrench-screwdriver" class="text-primary" />
            {{ $serviceId ? 'Edit Data Service Eksternal' : 'Catat Service Eksternal Baru' }}
        </h3>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">No. Surat Jalan (SJ)</label>
                    <x-mary-input wire:model="no_sj" placeholder="SJ/2026/001" />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Service Center / Bengkel *</label>
                    <select wire:model="m_service_center_id" class="select select-bordered w-full">
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($serviceCenters as $sc)
                            <option value="{{ $sc->id }}">{{ $sc->nama_service }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Barang yang Diservice *</label>
                    <select wire:model.live="m_barang_id" class="select select-bordered w-full">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barangs as $b)
                            <option value="{{ $b->id }}">{{ $b->kode_barang }} - {{ $b->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Pemakai</label>
                    <select wire:model="m_pemakai_id" class="select select-bordered w-full">
                        <option value="">-- Pilih Pemakai --</option>
                        @foreach($pemakais as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Tgl Kirim / Service *</label>
                    <input type="date" wire:model="tgl_service" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Tgl Selesai (Diambil)</label>
                    <input type="date" wire:model="tgl_selesai" class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Biaya Service (Rp)</label>
                    <input type="number" wire:model="biaya" class="input input-bordered w-full font-mono" placeholder="0" />
                </div>
            </div>

            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Kerusakan / Keluhan *</label>
                <textarea wire:model="kerusakan" class="textarea textarea-bordered w-full h-20" placeholder="Deskripsi kerusakan..."></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Analisa Vendor</label>
                    <textarea wire:model="analisa" class="textarea textarea-bordered w-full h-20" placeholder="Hasil analisa teknisi vendor..."></textarea>
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Solusi / Tindakan</label>
                    <textarea wire:model="solusi" class="textarea textarea-bordered w-full h-20" placeholder="Sparepart yang diganti / perbaikan..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-content/10">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md" wire:loading.attr="disabled">
                    <span wire:loading class="loading loading-spinner loading-xs"></span>
                    Simpan Service
                </button>
            </div>
        </form>
    </x-mary-modal>
</div>
