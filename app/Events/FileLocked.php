<?php

namespace App\Events;

use App\Models\OrderFile;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileLocked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $file;

    public function __construct(OrderFile $file)
    {
        $this->file = $file;
    }

    public function broadcastOn()
    {
        return new Channel('order');
    }
}
