<?php

namespace App\Domain\Security\Services;

use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Support\Str;

class SecurityEventRecorder
{
    public function record(string $type, string $severity, string $result, ?User $user = null, array $context = []): SecurityEvent
    {
        $request = request();
        return SecurityEvent::create([
            'event_ulid' => (string) Str::ulid(),
            'user_id' => $user?->id,
            'event_type' => $type,
            'severity' => $severity,
            'result' => $result,
            'username_snapshot' => $user?->username ?? ($context['username'] ?? null),
            'ip_address' => $request?->ip(),
            'user_agent' => Str::limit((string) $request?->userAgent(), 1000, ''),
            'request_id' => $request?->attributes->get('request_id'),
            'context' => collect($context)->except(['password', 'password_confirmation', 'token', 'secret'])->all(),
            'occurred_at' => now(),
        ]);
    }
}
