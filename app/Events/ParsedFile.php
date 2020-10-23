<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParsedFile implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $uuid;
    public $payload;

    public function __construct($message)
    {
        $this->uuid = $message->headers->uuid;
        $this->payload = $message->payload;
    }

    public function broadcastOn(): array
    {
        return ['pie-'.$this->uuid];
    }

    public function broadcastAs(): string
    {
        return 'parsed-file';
    }
}
