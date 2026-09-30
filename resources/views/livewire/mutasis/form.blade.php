<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('mutasis') }}" class="p-2 rounded-xl text-base-content/60 hover:text-base-content hover:bg-base-200 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-5 h-5" />
        </a>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight">
                {{ $mutasiId ? 'Edit Mutasi Barang' : 'Buat Mutasi Barang' }}
            </h1>
            <p class="text-sm opacity-60 mt-0.5">Form transaksi pengadaan, penjualan, atau mutasi perpindahan pemakai</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Card 1: Informasi Utama Mutasi --}}
        <div class="bg-base-100 rounded-2xl p-6 sm:p-8 border border-base-content/10 shadow-sm space-y-6">
            <h2 class="text-base font-bold border-b border-base-content/10 pb-3">Informasi Utama Mutasi</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider opacity-70 mb-1.5">Jenis Mutasi *</label>
                    <select wire:model.live="jenis_mutasi" class="select select-bordered w-full rounded-xl text-sm font-semibold">
                        <option value="pembelian">Pembelian / Pengadaan</option>
                        <option value="penjualan">Penjualan / Disposal</option>
                        <option value="perpindahan">Perpindahan Pemakai</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider opacity-70 mb-1.5">No. Mutasi (Auto)</label>
                    <input type="text" value="{{ $no_mutasi_preview }}" disabled class="input input-bordered w-full rounded-xl bg-base-200/50 text-base-content/60 font-mono text-sm font-semibold" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider opacity-70 mb-1.5">Tanggal Mutasi *</label>
                    <input type="date" wire:model="tgl_mutasi" required class="input input-bordered w-full rounded-xl text-sm font-mono" />
                </div>
            </div>

            @if($jenis_mutasi !== 'perpindahan')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider opacity-70 mb-1.5">Supplier / Vendor {{ $jenis_mutasi === 'pembelian' ? '*' : '' }}</label>
                        <x-mary-choices-offline
                            wire:model="m_supplier_id"
                            :options="$supplierOptions"
                            single
                            searchable
                            clearable
                            placeholder="Pilih atau ketik supplier..."
                        />
                        @error('m_supplier_id') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider opacity-70 mb-1.5">Keterangan</label>
                        <x-mary-input wire:model="keterangan" placeholder="Catatan transaksi..." class="rounded-xl" />
                    </div>
                </div>
            @endif
        </div>

        {{-- Card 2: Daftar Barang Mutasi --}}
        <div class="bg-base-100 rounded-2xl p-6 sm:p-8 border border-base-content/10 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-base-content/10 pb-3">
                <h2 class="text-base font-bold">Daftar Barang Mutasi</h2>
                <button type="button" wire:click="addDetail" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold hover:bg-emerald-500/20 transition-colors">
                    <x-mary-icon name="o-plus" class="w-3.5 h-3.5" />
                    <span>Tambah Baris</span>
                </button>
            </div>

            <div class="space-y-3">
                @foreach($details as $idx => $item)
                    <div wire:key="detail-row-{{ $idx }}-{{ $item['m_barang_id'] ?? '' }}" class="p-4 rounded-xl border border-base-content/10 bg-base-200/30 flex flex-col md:flex-row items-center gap-3">
                        <div class="flex-1 w-full">
                            <label class="block text-[11px] font-bold opacity-60 uppercase tracking-wider mb-1">
                                Pilih Barang #{{ $idx + 1 }} *
                            </label>
                            <div wire:key="form-barang-wrapper-{{ $idx }}-{{ $item['m_barang_id'] ?? 'none' }}-{{ implode('_', array_column($rowBarangOptions[$idx] ?? [], 'id')) }}">
                                <x-mary-choices-offline
                                    :id="'barang_select_' . $idx"
                                    wire:model.live="details.{{ $idx }}.m_barang_id"
                                    :options="$rowBarangOptions[$idx] ?? $barangOptions"
                                    single
                                    searchable
                                    clearable
                                    placeholder="Pilih atau ketik barang..."
                                />
                            </div>
                            @error("details.{$idx}.m_barang_id") <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                        </div>

                        @if($jenis_mutasi === 'perpindahan')
                            <div class="w-full md:w-64">
                                <label class="block text-[11px] font-bold opacity-60 uppercase tracking-wider mb-1">
                                    Pemakai Baru *
                                </label>
                                <x-mary-choices-offline
                                    :id="'pemakai_select_' . $idx"
                                    wire:model="details.{{ $idx }}.pemakai_baru"
                                    :options="$pemakaiOptions"
                                    single
                                    searchable
                                    clearable
                                    placeholder="Pilih pemakai baru..."
                                />
                                @error("details.{$idx}.pemakai_baru") <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div class="w-full md:w-44">
                                <label class="block text-[11px] font-bold opacity-60 uppercase tracking-wider mb-1">
                                    Harga (Rp)
                                </label>
                                <input type="number" wire:model="details.{{ $idx }}.harga" min="0" placeholder="0" class="input input-bordered w-full rounded-xl text-sm font-mono" />
                            </div>
                        @endif

                        @if(count($details) > 1)
                            <div class="pt-4 md:pt-5">
                                <button type="button" wire:click="removeDetail({{ $idx }})" class="p-2 rounded-lg text-base-content/40 hover:text-error hover:bg-error/10 transition-colors" title="Hapus Baris">
                                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('mutasis') }}" class="btn btn-ghost btn-sm rounded-xl">Batal</a>
            <button type="submit" class="btn btn-primary btn-sm px-6 rounded-xl font-bold shadow-md shadow-primary/20" wire:loading.attr="disabled">
                <span wire:loading class="loading loading-spinner loading-xs"></span>
                Simpan Transaksi Mutasi
            </button>
        </div>
    </form>
</div>
