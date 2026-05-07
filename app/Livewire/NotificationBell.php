<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public function markRead(string $id): void
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->update(['read_at' => now()]);
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function render()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->limit(15)
            ->get();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
            'unreadCount'   => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
