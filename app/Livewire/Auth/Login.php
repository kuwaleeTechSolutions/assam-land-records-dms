<?php

namespace App\Livewire\Auth;

use App\Domain\Security\Services\SecurityEventRecorder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    public string $username = '';
    public string $password = '';
    public bool $remember = false;

    public function authenticate(SecurityEventRecorder $events): void
    {
        $this->validate(['username' => ['required','string','max:100'], 'password' => ['required','string','max:500']]);
        $key = Str::lower($this->username).'|'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $events->record('LOGIN_THROTTLED', 'HIGH', 'DENIED', null, ['username' => $this->username]);
            throw ValidationException::withMessages(['username' => 'Too many login attempts. Try again in '.RateLimiter::availableIn($key).' seconds.']);
        }
        $user = User::where('username', $this->username)->first();
        if (! $user || ! $user->isActive() || ! Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key, 60);
            if ($user) $user->increment('failed_login_count');
            $events->record('LOGIN_FAILED', 'MEDIUM', 'DENIED', $user, ['username' => $this->username]);
            throw ValidationException::withMessages(['username' => 'The supplied credentials are incorrect.']);
        }
        RateLimiter::clear($key);
        session()->regenerate();
        $user->forceFill(['failed_login_count' => 0, 'locked_until' => null, 'last_login_at' => now(), 'last_login_ip' => request()->ip()])->save();
        $events->record('LOGIN_SUCCESS', 'INFO', 'SUCCESS', $user);
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render() { return view('livewire.auth.login'); }
}
