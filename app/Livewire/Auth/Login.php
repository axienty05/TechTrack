<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    public string $login = '';
    public string $password = '';
    public bool $remember = false;

    public function authenticate()
    {
        $this->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([
            $fieldType => $this->login,
            'password' => $this->password,
        ], $this->remember)) {
            // Cek apakah user masih aktif
            if (!auth()->user()->is_active) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->addError('login', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
                return;
            }

            session()->regenerate();
            return redirect()->intended('/');
        }

        $this->addError('login', 'Kredensial yang Anda masukkan tidak valid.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
