<div>
    {{-- Header --}}
    <x-mary-header :title="$item ? 'Edit Barang: ' . $item->kode_barang : 'Tambah Barang Baru'" separator>
        <x-slot:actions>
            <x-mary-button label="Kembali" icon="o-arrow-left" link="{{ route('inventory.items') }}" class="btn-ghost" />
        </x-slot:actions>
    </x-mary-header>

    {{-- Tab hanya muncul saat edit --}}
    @if($item)
        <div class="flex gap-2 mb-6 border-b border-base-content/10">
            <button type="button"
                wire:click="$set('activeTab', 'detail')"
                @class(['btn btn-sm gap-2', 'btn-primary' => $activeTab === 'detail', 'btn-ghost' => $activeTab !== 'detail'])>
                <x-mary-icon name="o-information-circle" class="w-4 h-4" />
                Detail Barang
            </button>
            <button type="button"
                wire:click="$set('activeTab', 'history')"
                @class(['btn btn-sm gap-2', 'btn-primary' => $activeTab === 'history', 'btn-ghost' => $activeTab !== 'history'])>
                <x-mary-icon name="o-clock" class="w-4 h-4" />
                Riwayat & Maintenance
                @if(count($histories) > 0)
                    <span class="badge badge-sm">{{ count($histories) }}</span>
                @endif
            </button>
        </div>
    @endif

    {{-- ═══════════════════════ TAB DETAIL ═══════════════════════ --}}
    <div @class(['hidden' => $item && $activeTab !== 'detail'])>
        <x-mary-form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <x-mary-card title="Informasi Utama">
                    <div class="grid gap-4">
                        <x-mary-input
                            label="Kode Barang *"
                            wire:model="kode_barang"
                            placeholder="BRG-0001"
                            required />

                        <x-mary-input
                            label="Nama Barang *"
                            wire:model="nama_barang"
                            placeholder="Laptop Lenovo ThinkPad"
                            required />

                        <x-mary-input
                            label="Merk / Model"
                            wire:model="merk_model"
                            placeholder="Lenovo T14 Gen 3" />

                        <x-mary-input
                            label="Serial Number"
                            wire:model="serial_number"
                            placeholder="PF123456" />

                        <x-mary-select
                            label="Kategori *"
                            wire:model="kategori"
                            placeholder="Pilih Kategori"
                            required
                            :options="[
                                ['id' => 'komputer',  'name' => 'Komputer'],
                                ['id' => 'laptop',    'name' => 'Laptop'],
                                ['id' => 'monitor',   'name' => 'Monitor'],
                                ['id' => 'printer',   'name' => 'Printer'],
                                ['id' => 'jaringan',  'name' => 'Jaringan'],
                                ['id' => 'ups',       'name' => 'UPS'],
                                ['id' => 'sparepart', 'name' => 'Sparepart'],
                                ['id' => 'lain_lain', 'name' => 'Lain-lain'],
                            ]" />
                    </div>
                </x-mary-card>

                <x-mary-card title="Lokasi & Status">
                    <div class="grid gap-4">
                        <x-mary-input
                            label="Lokasi Spesifik"
                            wire:model="lokasi"
                            placeholder="Misal: Meja Pak Budi, Gudang IT" />

                        <x-mary-select
                            label="Department Pengguna"
                            wire:model="department_id"
                            placeholder="Pilih Department"
                            :options="$departments" />

                        <x-mary-select
                            label="Kondisi Saat Ini *"
                            wire:model="kondisi"
                            required
                            :options="[
                                ['id' => 'aktif',         'name' => 'Aktif Digunakan'],
                                ['id' => 'cadangan',      'name' => 'Cadangan (Standby)'],
                                ['id' => 'rusak',         'name' => 'Rusak'],
                                ['id' => 'sedang_servis', 'name' => 'Sedang Servis'],
                                ['id' => 'dijual',        'name' => 'Dijual / Dihibahkan'],
                            ]" />
                    </div>
                </x-mary-card>

                <x-mary-card title="Pembelian & Keterangan" class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-mary-input
                            type="date"
                            label="Tanggal Perolehan / Pembelian"
                            wire:model="tgl_perolehan" />

                        <x-mary-input
                            type="number"
                            label="Harga Perolehan (Rp)"
                            wire:model="harga_perolehan"
                            placeholder="0" />
                    </div>
                    <div class="mt-4">
                        <x-mary-textarea
                            label="Keterangan / Spesifikasi"
                            wire:model="keterangan"
                            placeholder="RAM 16GB, SSD 512GB, dsb..."
                            rows="3" />
                    </div>
                </x-mary-card>

            </div>

            <x-slot:actions>
                <x-mary-button label="Batal" link="{{ route('inventory.items') }}" class="btn-ghost" />
                <x-mary-button label="Simpan Barang" type="submit" class="btn-primary" icon="o-check" spinner="save" />
            </x-slot:actions>
        </x-mary-form>
    </div>

    {{-- ═══════════════════════ TAB HISTORY ══════════════════════ --}}
    @if($item)
    <div @class(['hidden' => $activeTab !== 'history'])>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Form Tambah Riwayat --}}
            <x-mary-card title="Tambah Catatan">
                <x-mary-form wire:submit="saveHistory">
                    <div class="grid gap-4">
                        <x-mary-input
                            type="date"
                            label="Tanggal Kejadian *"
                            wire:model="history_date"
                            required />

                        <x-mary-select
                            label="Jenis Aksi *"
                            wire:model="history_action"
                            required
                            :options="[
                                ['id' => 'perbaikan',     'name' => 'Perbaikan / Servis'],
                                ['id' => 'mutasi',        'name' => 'Mutasi / Pindah Lokasi'],
                                ['id' => 'update_status', 'name' => 'Update Status / Kondisi'],
                                ['id' => 'catatan',       'name' => 'Catatan Umum'],
                            ]" />

                        <x-mary-textarea
                            label="Keterangan *"
                            wire:model="history_description"
                            placeholder="Ganti RAM, Pindah ke meja Pak Budi..."
                            rows="4"
                            required />
                    </div>
                    <x-slot:actions>
                        <x-mary-button label="Simpan Riwayat" type="submit" class="btn-primary w-full" icon="o-plus" spinner="saveHistory" />
                    </x-slot:actions>
                </x-mary-form>
            </x-mary-card>

            {{-- List Riwayat --}}
            <div class="lg:col-span-2">
                <x-mary-card title="Jejak Riwayat">
                    @forelse($histories as $history)
                        <div class="flex gap-4 border-b border-base-content/10 pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                            <div class="flex-none">
                                @php
                                    $icon  = match($history->action) {
                                        'perbaikan'     => 'o-wrench-screwdriver',
                                        'mutasi'        => 'o-arrows-right-left',
                                        'update_status' => 'o-arrow-path',
                                        default         => 'o-document-text',
                                    };
                                    $color = match($history->action) {
                                        'perbaikan'     => 'text-warning',
                                        'mutasi'        => 'text-info',
                                        'update_status' => 'text-success',
                                        default         => 'text-base-content/50',
                                    };
                                @endphp
                                <div class="bg-base-200 p-2 rounded-full mt-1">
                                    <x-mary-icon name="{{ $icon }}" class="w-5 h-5 {{ $color }}" />
                                </div>
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="flex justify-between items-start gap-2">
                                    <div>
                                        <span class="font-semibold">{{ Str::headline($history->action) }}</span>
                                        <div class="text-xs opacity-60 mt-0.5">
                                            {{ $history->action_date->format('d M Y') }}
                                            &bull; {{ $history->user->name ?? 'System' }}
                                        </div>
                                    </div>
                                    <x-mary-button
                                        icon="o-trash"
                                        class="btn-xs btn-ghost text-error flex-none"
                                        wire:click="deleteHistory({{ $history->id }})"
                                        wire:confirm="Hapus catatan riwayat ini?" />
                                </div>
                                <p class="mt-2 text-sm whitespace-pre-wrap">{{ $history->description }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 opacity-40">
                            <x-mary-icon name="o-inbox" class="w-10 h-10 mx-auto mb-2" />
                            <p class="text-sm">Belum ada riwayat untuk barang ini.</p>
                        </div>
                    @endforelse
                </x-mary-card>
            </div>

        </div>
    </div>
    @endif
</div>
