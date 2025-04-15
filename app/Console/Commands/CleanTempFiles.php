<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-temp-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directories = Storage::disk('temp')->directories();

        collect($directories)->each(function ($dir) {
            if (now()->diffInHours(Storage::disk('temp')->lastModified($dir)) > 24) {
                Storage::disk('temp')->deleteDirectory($dir);
            }
        });

        $this->info('Cleaned up temporary files');
    }
}
