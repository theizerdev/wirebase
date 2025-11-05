<?php

namespace App\Livewire\Admin\Notifications;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Notification;
use Livewire\WithPagination;

class Index extends Component
{
    use HasDynamicLayout;


    use WithPagination;

    public function markAsRead($notificationId)
    {
        $notification = Notification::where('user_id', auth()->id())->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.admin.notifications.index',[
            'notifications' => Notification::where('user_id', auth()->id())->latest()->paginate(100),
        ])->layout($this->getLayout());
    }
}




