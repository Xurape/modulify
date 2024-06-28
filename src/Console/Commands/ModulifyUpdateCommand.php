<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function Laravel\Prompts\spin;

final class ModulifyUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "modulify:update";

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = "Update modulify ✨";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //TODO: update command
    }
}
