<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessOrderFolder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order, public string $tempPath)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $files = Storage::disk('temp')->allFiles($this->tempPath);

        DB::beginTransaction();

        try {
            foreach ($files as $filePath) {
                $relativePath = str_replace("{$this->tempPath}/", '', $filePath);
                $targetPath = "{$this->order->order_number}/original/{$relativePath}";

                // Copy to public disk (use the 'public' disk instead of 'original')
                $content = Storage::disk('temp')->get($filePath);
                Storage::disk('public')->put($targetPath, $content);

                OrderFile::create([
                    'order_id' => $this->order->id,
                    'filename' => basename($filePath),
                    'filepath' => $targetPath,
                ]);
            }

            DB::commit();

            Storage::disk('temp')->deleteDirectory($this->tempPath);
        } catch (\Throwable $e) {
            DB::rollBack();
            //log or notify error
            Log::error('Failed to process order files', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
