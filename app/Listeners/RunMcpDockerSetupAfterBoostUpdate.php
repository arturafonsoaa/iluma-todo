<?php

namespace App\Listeners;

use Illuminate\Console\Events\CommandFinished;
use Symfony\Component\Process\Process;

class RunMcpDockerSetupAfterBoostUpdate
{
    public function handle(CommandFinished $event): void
    {
        if ($event->command !== 'boost:update' || $event->exitCode !== 0) {
            return;
        }

        $process = new Process(['php', base_path('bin/setup-mcp-docker')]);
        $process->run();
    }
}
