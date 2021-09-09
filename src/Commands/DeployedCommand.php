<?php

namespace AdamGaskins\Deployed\Commands;

use Illuminate\Console\Command;

class DeployedCommand extends Command
{
    public $signature = 'skeleton';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
