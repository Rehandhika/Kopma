<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    public $nim = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'nim' => 'required|string',
        'password' => 'required|string',
    ];

    public function login()
    {
        $this->validate();

        if (!Auth::attempt(['nim' => $this->nim, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'nim' => 'NIM atau password salah.',
            ]);
        }

        session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest');
    }
}
