<?php

namespace App\Http\Middleware;

use App\Domain\Security\Services\SecurityEventRecorder;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function __construct(private SecurityEventRecorder $events) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && ! $user->isActive()) {
            $this->events->record('ACCESS_BLOCKED_INACTIVE_ACCOUNT', 'HIGH', 'DENIED', $user);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['username' => 'Your account is not permitted to access the system.']);
        }
        return $next($request);
    }
}
