<div class="space-y-4 sm:space-y-6 max-w-4xl mx-auto">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Pengaturan Profil Saya</h1>
        <p class="text-xs sm:text-sm opacity-60">Kelola informasi akun pribadi, foto profil, dan kata sandi keamanan</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        <!-- Foto Profil Card -->
        <div class="bg-base-100 p-4 sm:p-6 rounded-2xl shadow-md border border-base-content/5 flex flex-col items-center text-center space-y-4">
            <div class="flex items-center justify-center">
                @if($avatar)
                    <div class="rounded-full overflow-hidden shadow-xl border-4 border-primary bg-base-200" style="width: 110px; height: 110px; min-width: 110px; min-height: 110px;">
                        <img src="{{ $avatar->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover" style="width: 110px; height: 110px; object-fit: cover;" />
                    </div>
                @elseif($currentAvatar)
                    <div class="rounded-full overflow-hidden shadow-xl border-4 border-primary bg-base-200" style="width: 110px; height: 110px; min-width: 110px; min-height: 110px;">
                        <img src="{{ $currentAvatar }}" alt="Avatar" class="w-full h-full object-cover" style="width: 110px; height: 110px; object-fit: cover;" />
                    </div>
                @else
                    <div class="bg-neutral text-neutral-content rounded-full flex items-center justify-center font-bold text-2xl sm:text-3xl shadow-xl select-none" style="width: 110px; height: 110px; min-width: 110px; min-height: 110px;">
                        <span>{{ strtoupper(substr($name ?: 'U', 0, 2)) }}</span>
                    </div>
                @endif
            </div>

            <div>
                <h3 class="font-bold text-base sm:text-lg leading-tight">{{ auth()->user()->name }}</h3>
                <span class="badge badge-primary badge-sm uppercase font-bold text-[10px] mt-1">{{ auth()->user()->role }}</span>
            </div>

            <div class="w-full pt-2 border-t border-base-content/10 space-y-2">
                <label class="btn btn-outline btn-primary btn-sm w-full gap-2 cursor-pointer">
                    <x-mary-icon name="o-arrow-up-tray" class="w-4 h-4" />
                    <span>Pilih Foto Baru</span>
                    <input type="file" wire:model="avatar" accept="image/*" class="hidden" />
                </label>
                @if($currentAvatar)
                    <button type="button" wire:click="deleteAvatar" wire:confirm="Hapus foto profil ini?" class="btn btn-ghost btn-xs text-error w-full gap-1">
                        <x-mary-icon name="o-trash" class="w-3.5 h-3.5" /> Hapus Foto
                    </button>
                @endif
                <span class="text-[11px] opacity-50 block">Format PNG, JPG, JPEG (Maks 5MB)</span>
            </div>
        </div>

        <!-- Form Edit Data Diri -->
        <div class="md:col-span-2 space-y-4 sm:space-y-6">
            <div class="bg-base-100 p-4 sm:p-6 rounded-2xl shadow-md border border-base-content/5">
                <h2 class="text-base sm:text-lg font-bold flex items-center gap-2 mb-4">
                    <x-mary-icon name="o-identification" class="text-primary w-5 h-5 flex-shrink-0" />
                    <span>Informasi Data Diri</span>
                </h2>
                <form wire:submit="updateProfile" class="space-y-4">
                    <div>
                        <label class="label text-xs font-bold uppercase opacity-70">Nama Lengkap</label>
                        <x-mary-input wire:model="name" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label text-xs font-bold uppercase opacity-70">Username</label>
                            <x-mary-input wire:model="username" />
                        </div>
                        <div>
                            <label class="label text-xs font-bold uppercase opacity-70">Nomor Telepon / WA</label>
                            <x-mary-input wire:model="phone_number" placeholder="08xxxxxxxxxx" />
                        </div>
                    </div>
                    <div>
                        <label class="label text-xs font-bold uppercase opacity-70">Alamat Email</label>
                        <x-mary-input type="email" wire:model="email" />
                    </div>

                    <div class="pt-3 border-t border-base-content/10 flex justify-end">
                        <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md w-full sm:w-auto" wire:loading.attr="disabled">
                            <span wire:loading class="loading loading-spinner loading-xs"></span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Form Ganti Password -->
            <div class="bg-base-100 p-4 sm:p-6 rounded-2xl shadow-md border border-base-content/5">
                <h2 class="text-base sm:text-lg font-bold flex items-center gap-2 mb-4">
                    <x-mary-icon name="o-key" class="text-warning w-5 h-5 flex-shrink-0" />
                    <span>Ubah Kata Sandi</span>
                </h2>
                <form wire:submit="updatePassword" class="space-y-4">
                    <div>
                        <label class="label text-xs font-bold uppercase opacity-70">Kata Sandi Saat Ini</label>
                        <x-mary-input type="password" wire:model="current_password" placeholder="Masukkan password lama" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label text-xs font-bold uppercase opacity-70">Kata Sandi Baru</label>
                            <x-mary-input type="password" wire:model="new_password" placeholder="Minimal 6 karakter" />
                        </div>
                        <div>
                            <label class="label text-xs font-bold uppercase opacity-70">Konfirmasi Kata Sandi Baru</label>
                            <x-mary-input type="password" wire:model="new_password_confirmation" placeholder="Ulangi password baru" />
                        </div>
                    </div>

                    <div class="pt-3 border-t border-base-content/10 flex justify-end">
                        <button type="submit" class="btn btn-warning btn-sm px-6 shadow-md w-full sm:w-auto" wire:loading.attr="disabled">
                            <span wire:loading class="loading loading-spinner loading-xs"></span>
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>