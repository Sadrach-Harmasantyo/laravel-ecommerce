<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

#[Title('Register - TokoOnline')]
class RegisterPage extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    
    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required'
    ];
    
    public function mount()
    {
        if (Auth::check()) {
            return redirect()->route('index');
        }
    }
    
    public function register()
    {
        $this->validate();
        
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        
        Auth::login($user);
        
        session()->flash('success', 'Account created successfully!');
        
        return redirect()->route('index');
    }
    
    public function render()
    {
        return view('livewire.auth.register-page');
    }
}
