<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, WithFileUploads, Toast;

    public string $search = '';
    public bool $showModal = false;
    public ?int $userId = null;

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'it_support';
    public string $phone_number = '';
    public bool $is_active = true;

    // Avatar upload
    public $avatar = null;
    public ?string $currentAvatar = null;

    public function openCreateModal()
    {
        $this->reset(['userId', 'name', 'username', 'email', 'password', 'phone_number', 'avatar', 'currentAvatar']);
        $this->role = 'it_support';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
        $this->password = '';
        $this->role = $user->role ?? 'it_support';
        $this->phone_number = $user->phone_number ?? '';
        $this->is_active = (bool) $user->is_active;
        $this->currentAvatar = $user->avatar ? asset('storage/' . $user->avatar) : null;
        $this->avatar = null;
        $this->showModal = true;
    }

    public function deleteAvatar()
    {
        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->update(['avatar' => null]);
            $this->currentAvatar = null;
            $this->avatar = null;
            $this->success('Foto profil berhasil dihapus.');
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $this->userId,
            'email' => 'required|email|max:100|unique:users,email,' . $this->userId,
            'role' => 'required|in:admin,it_lead,it_support',
            'password' => $this->userId ? 'nullable|min:6' : 'required|min:6',
            'avatar' => 'nullable|image|max:5120', // max 5MB
        ]);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'phone_number' => $this->phone_number,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            if ($this->avatar) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $data['avatar'] = $this->avatar->store('avatars', 'public');
            }
            $user->update($data);
            $this->success('Akun pengguna berhasil diperbarui!');
        } else {
            if ($this->avatar) {
                $data['avatar'] = $this->avatar->store('avatars', 'public');
            }
            User::create($data);
            $this->success('Pengguna baru berhasil ditambahkan!');
        }

        $this->showModal = false;
    }

    public function delete(int $id)
    {
        if ($id === auth()->id()) {
            $this->error('Anda tidak dapat menghapus akun Anda sendiri!');
            return;
        }

        $user = User::findOrFail($id);
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->delete();
        $this->success('Pengguna berhasil dihapus!');
    }

    public function render()
    {
        $term = '%' . $this->search . '%';
        $users = User::when($this->search, function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('username', 'like', $term)
                  ->orWhere('email', 'like', $term);
            })
            ->orderBy('id')
            ->paginate(10);

        return view('livewire.users.index', ['users' => $users]);
    }
}