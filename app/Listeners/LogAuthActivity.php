<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthActivity
{
    /**
     * Handle authentication events.
     */
    public function handle(object $event): void
    {
        if ($event instanceof Login) {
            $this->writeLog(
                userId: $event->user->id,
                action: 'login',
                description: 'User logged in'
            );
        }

        if ($event instanceof Logout) {
            $this->writeLog(
                userId: $event->user?->id,
                action: 'logout',
                description: 'User logged out'
            );
        }
    }

    private function writeLog(?int $userId, string $action, string $description): void
    {
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'occurred_at' => now(),
        ]);
    }
}
