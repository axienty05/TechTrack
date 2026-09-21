<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;

class Edit extends Component
{
    use WithFileUploads, Toast;

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone_number = '';
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public $avatar = null;
    public ?string $currentAvatar = null;

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
        $this->phone_number = $user->phone_number ?? '';
        $this->currentAvatar = $user->avatar && Storage::disk('public')->exists($user->avatar)
            ? asset('storage/' . $user->avatar)
            : null;
    }

    public function deleteAvatar()
    {
        $user = auth()->user();
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->update(['avatar' => null]);
        $this->currentAvatar = null;
        $this->avatar = null;
        $this->success('Foto profil berhasil dihapus.');
    }

    public function updateProfile()
    {
        $user = auth()->user();

        $this->validate([
            'name'         => 'required|string|max:100',
            'username'     => 'required|string|max:50|unique:users,username,' . $user->id,
            'email'        => 'required|email|max:100|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'avatar'       => 'nullable|image|max:5120',
        ]);

        $data = [
            'name'         => $this->name,
            'username'     => $this->username,
            'email'        => $this->email,
            'phone_number' => $this->phone_number,
        ];

        if ($this->avatar) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $this->avatar->store('avatars', 'public');
            $this->currentAvatar = asset('storage/' . $data['avatar']);
            $this->avatar = null;
        }

        $user->update($data);

        // Refresh data dari database
        $fresh = $user->fresh();
        $this->name         = $fresh->name;
        $this->username     = $fresh->username ?? '';
        $this->email        = $fresh->email ?? '';
        $this->phone_number = $fresh->phone_number ?? '';

        $this->success('Profil dan foto berhasil diperbarui!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password lama tidak sesuai.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->success('Password berhasil diubah!');
    }

    public function render()
    {
        return view('livewire.profile.edit');
    }
}