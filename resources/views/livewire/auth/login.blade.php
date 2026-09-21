<div class="w-full max-w-md bg-base-100 p-8 rounded-2xl shadow-2xl border border-base-content/10">
    <div class="text-center mb-8">
        <div class="inline-flex p-3 rounded-full bg-primary/10 text-primary mb-3">
            <x-mary-icon name="o-cpu-chip" class="w-10 h-10" />
        </div>
        <h1 class="text-2xl font-bold tracking-tight">TechTrack Login</h1>
        <p class="text-sm opacity-60 mt-1">IT Worklog &amp; Routine Maintenance System</p>
    </div>

    <form wire:submit="authenticate" class="space-y-4">
        <x-mary-input label="Username / Email" wire:model="login" icon="o-user" placeholder="Masukkan username atau email" />
        <x-mary-input label="Password" type="password" wire:model="password" icon="o-key" placeholder="Masukkan password" />

        <div class="flex items-center justify-between pt-2">
            <label class="cursor-pointer label gap-2">
                <input type="checkbox" wire:model="remember" class="checkbox checkbox-primary checkbox-sm" />
                <span class="label-text text-xs">Ingat Saya</span>
            </label>
        </div>

        @error('login')
            <div class="alert alert-error text-sm py-2">{{ $message }}</div>
        @enderror

        <div class="pt-4">
            <x-mary-button label="Masuk ke Sistem" type="submit" icon="o-arrow-right-on-rectangle" class="btn-primary w-full" spinner="authenticate" />
        </div>
    </form>
</div>