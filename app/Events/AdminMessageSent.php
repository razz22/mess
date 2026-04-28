<?php

namespace App\Events;

use App\Models\AdminMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public AdminMessage $message) {}

    public function broadcastOn(): array
    {
        return [
            // Chat page for both owner and admin
            new PrivateChannel('mess-suggestions.' . $this->message->mess_id),
            // Admin-wide channel for popup notifications on any page
            new PrivateChannel('admin-suggestions'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id'          => $this->message->id,
            'mess_id'     => $this->message->mess_id,
            'mess_name'   => $this->message->mess->name,
            'owner_name'  => $this->message->owner->name,
            'sender_role' => $this->message->sender_role,
            'message'     => $this->message->message,
            'created_at'  => $this->message->created_at->format('d M Y, h:i A'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
