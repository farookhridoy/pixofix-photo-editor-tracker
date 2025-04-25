<?php

namespace App\Console\Commands;

use App\Models\OrderFile;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UnlockFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:unlock-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unlock files locked for more than 2 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredAt = Carbon::now()->subMinutes(2);

        $files = OrderFile::whereNotNull('locked_by')
            ->whereNull('claimed_by')
            ->where('locked_at', '<=', $expiredAt)
            ->get();

        foreach ($files as $file) {
            $file->update([
                'locked_by' => null,
                'locked_at' => null,
            ]);

            event(new \App\Events\FileUnlocked($file));

            fileLogGenerate($file->id, 'unlocked', 'Lock expired or released');
        }

        $this->info("Unlocked {$files->count()} files.");
    }
}
