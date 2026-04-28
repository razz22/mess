<?php

namespace App\Events;

use App\Models\MessNotice;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class NoticePublished implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public MessNotice $notice) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('mess.' . $this->notice->mess_id . '.notices'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id'           => $this->notice->id,
            'title'        => $this->notice->title,
            'body_preview' => Str::limit(strip_tags($this->notice->body), 100),
            'published_at' => $this->notice->published_at->toIso8601String(),
            'author'       => $this->notice->author->name,
            'mess_id'      => $this->notice->mess_id,
        ];
    }

    public function broadcastAs(): string
    {
        return 'notice.published';
    }
}
