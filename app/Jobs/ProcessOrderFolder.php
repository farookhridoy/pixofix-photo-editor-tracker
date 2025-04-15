<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProcessOrderFolder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order, public string $tempPath) {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $files = Storage::disk('temp')->allFiles($this->tempPath);

        foreach ($files as $filePath) {
            $relativePath = str_replace("{$this->tempPath}/", '', $filePath);

            Storage::disk('original')->put(
                "{$this->order->id}/{$relativePath}",
                Storage::disk('temp')->get($filePath)
            );

            OrderFile::create([
                'order_id' => $this->order->id,
                'filename' => 'N/A',
                'filepath' => $relativePath
            ]);
        }

        Storage::disk('temp')->deleteDirectory($this->tempPath);
    }
}
