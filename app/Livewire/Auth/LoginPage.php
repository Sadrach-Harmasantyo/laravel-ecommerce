<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[Title('Login - TokoOnline')]
class LoginPage extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    
    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];
    
    public function mount()
    {
        if (Auth::check()) {
            return redirect()->route('index');
        }
    }
    
    public function login()
    {
        $this->validate();
        
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->flash('success', 'You have been successfully logged in!');
            return redirect()->intended(route('index'));
        }
        
        $this->addError('email', 'The provided credentials do not match our records.');
    }
    
    public function render()
    {
        return view('livewire.auth.login-page');
    }
}
