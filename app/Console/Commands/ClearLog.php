<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the laravel.log file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $logPath = config('logging.channels.single.path');

        if (!$logPath || !file_exists($logPath)) {
            $this->error('Log file clearing is not available for current channel.');

            return;
        }

        exec(': > ' . $logPath, $output, $code);

        if ($code === 0) {
            $this->info('Log file cleared.');
        }
    }
}
