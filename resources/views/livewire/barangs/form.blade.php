<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight">
                {{ $barangId ? 'Edit Barang / Aset IT' : 'Tambah Barang Baru' }}
            </h1>
            <p class="text-xs sm:text-sm opacity-60">
                {{ $barangId ? "Mengubah data aset #{$kode_barang}" : 'Daftarkan perangkat/aset IT baru ke sistem' }}
            </p>
        </div>
        <a href="{{ route('barangs') }}" class="btn btn-ghost btn-sm gap-2">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            Kembali
        </a>
    </div>

    <div class="bg-base-100 p-6 rounded-2xl shadow-md border border-base-content/5">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Kode Barang *</label>
                    <label class="input w-full font-mono bg-base-200/50 flex items-center">
                        <input type="text" wire:model="kode_barang" placeholder="B/000001" readonly class="grow cursor-not-allowed text-sm" />
                    </label>
                    @error('kode_barang')
                        <div class="text-error text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Kategori *</label>
                    <x-mary-choices-offline
                        wire:model="kategori"
                        :options="$kategoriOptions"
                        single
                        searchable
                        clearable
                        placeholder="-- Pilih Kategori --"
                    />
                </div>
            </div>

            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Nama Barang / Tipe / Merk *</label>
                <x-mary-input wire:model="nama_barang" placeholder="Contoh: Laptop Dell Latitude 3420 Core i5" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Serial Number (SN)</label>
                    <x-mary-input wire:model="serial_number" placeholder="Contoh: CN-0R8568-..." />
                </div>
                <div>
                    <label class="label text-xs font-bold uppercase opacity-70">Status *</label>
                    <select wire:model="status" class="select select-bordered w-full">
                        @foreach($statusList as $st)
                            <option value="{{ $st }}">{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Pemakai Saat Ini</label>
                <x-mary-choices-offline
                    wire:model="m_pemakai_id"
                    :options="$pemakaiOptions"
                    single
                    searchable
                    clearable
                    placeholder="-- Belum Ditugaskan / Di Gudang IT --"
                />
            </div>

            <div>
                <label class="label text-xs font-bold uppercase opacity-70">Keterangan / Spesifikasi Tambahan</label>
                <textarea wire:model="keterangan" class="textarea textarea-bordered w-full h-24" placeholder="RAM 16GB, SSD 512GB, kondisi mulus..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-content/10">
                <a href="{{ route('barangs') }}" class="btn btn-ghost btn-sm">Batal</a>
                <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md" wire:loading.attr="disabled">
                    <span wire:loading class="loading loading-spinner loading-xs"></span>
                    Simpan Barang
                </button>
            </div>
        </form>
    </div>
</div>
