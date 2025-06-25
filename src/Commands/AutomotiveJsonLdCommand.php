<?php

namespace Bestfitcz\AutomotiveJsonLd\Commands;

use Illuminate\Console\Command;

class AutomotiveJsonLdCommand extends Command
{
    public $signature = 'automotive-json-ld';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
