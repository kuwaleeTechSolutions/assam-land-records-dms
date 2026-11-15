<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function logout(): void
    {
        app(\App\Domain\Security\Services\SecurityEventRecorder::class)->record('LOGOUT', 'INFO', 'SUCCESS', auth()->user());
        auth()->logout(); request()->session()->invalidate(); request()->session()->regenerateToken();
        $this->redirectRoute('login', navigate: true);
    }
    public function render() { return view('livewire.dashboard'); }
}
