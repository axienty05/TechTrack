<div>
    <x-mary-header title="Daftar Barang & Aset IT" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-mary-input placeholder="Cari barang..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button icon="o-funnel" tooltip="Filter" @click="$refs.filter_modal.showModal()" class="btn-ghost" />
            <x-mary-button icon="o-plus" class="btn-primary" link="{{ route('inventory.items.create') }}" label="Tambah Barang" />
        </x-slot:actions>
    </x-mary-header>

    <x-mary-card>
        <x-mary-table :headers="$headers" :rows="$items" :sort-by="$sortBy" with-pagination per-page="10" striped>
            @scope('cell_kategori', $item)
                <x-mary-badge :value="Str::headline($item->kategori)" class="badge-ghost" />
            @endscope

            @scope('cell_kondisi', $item)
                @php
                    $color = match($item->kondisi) {
                        'aktif' => 'badge-success',
                        'rusak' => 'badge-error',
                        'sedang_servis' => 'badge-warning',
                        'cadangan' => 'badge-info',
                        default => 'badge-ghost',
                    };
                @endphp
                <x-mary-badge :value="Str::headline($item->kondisi)" class="{{ $color }}" />
            @endscope

            @scope('actions', $item)
                <div class="flex gap-2">
                    <x-mary-button icon="o-pencil-square" class="btn-sm btn-ghost text-blue-500" link="{{ route('inventory.items.edit', $item->id) }}" tooltip="Edit" />
                    <x-mary-button icon="o-trash" class="btn-sm btn-ghost text-red-500" wire:click="delete({{ $item->id }})" wire:confirm="Yakin ingin menghapus barang ini?" tooltip="Hapus" />
                </div>
            @endscope
        </x-mary-table>
    </x-mary-card>

    <dialog id="filter_modal" class="modal" wire:ignore.self>
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Filter Data</h3>
            
            <div class="grid gap-4">
                <x-mary-select 
                    label="Kategori" 
                    wire:model.live="kategori" 
                    :options="[
                        ['id' => 'komputer', 'name' => 'Komputer'],
                        ['id' => 'laptop', 'name' => 'Laptop'],
                        ['id' => 'monitor', 'name' => 'Monitor'],
                        ['id' => 'printer', 'name' => 'Printer'],
                        ['id' => 'jaringan', 'name' => 'Jaringan'],
                        ['id' => 'ups', 'name' => 'UPS'],
                        ['id' => 'sparepart', 'name' => 'Sparepart'],
                        ['id' => 'lain_lain', 'name' => 'Lain-lain'],
                    ]" 
                    placeholder="Semua Kategori" 
                />

                <x-mary-select 
                    label="Kondisi" 
                    wire:model.live="kondisi" 
                    :options="[
                        ['id' => 'aktif', 'name' => 'Aktif'],
                        ['id' => 'cadangan', 'name' => 'Cadangan'],
                        ['id' => 'rusak', 'name' => 'Rusak'],
                        ['id' => 'sedang_servis', 'name' => 'Sedang Servis'],
                        ['id' => 'dijual', 'name' => 'Dijual/Dihibahkan'],
                    ]" 
                    placeholder="Semua Kondisi" 
                />
            </div>

            <div class="modal-action">
                <x-mary-button label="Reset" wire:click="clearFilters" class="btn-ghost" @click="$refs.filter_modal.close()" />
                <form method="dialog">
                    <x-mary-button label="Tutup" class="btn-primary" type="submit" />
                </form>
            </div>
        </div>
    </dialog>
</div>
