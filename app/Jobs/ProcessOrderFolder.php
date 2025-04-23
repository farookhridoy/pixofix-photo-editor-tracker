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
            // Fetch a category path
            $category = $this->order->category;
            $pathSlug = $category->getPathSlugs();

            foreach ($files as $filePath) {
                $relativePath = str_replace("{$this->tempPath}/", '', $filePath);

                // Build a full path using slugs
                $targetPath = "uploads/{$pathSlug}/{$this->order->order_number}/original/{$relativePath}";

                // Store to public disk (you can change to s3 if needed)
                $content = Storage::disk('temp')->get($filePath);
                Storage::disk('public')->put($targetPath, $content);

                //For AWS bucket if needed to upload on cloud
                //Storage::disk('s3')->put($targetPath, $content, 'public');

                // Save metadata
                OrderFile::create([
                    'order_id' => $this->order->id,
                    'filename' => basename($filePath),
                    'filepath' => $targetPath,
                ]);
            }

            DB::commit();

            // Clean up
            Storage::disk('temp')->deleteDirectory($this->tempPath);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to process order files', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
