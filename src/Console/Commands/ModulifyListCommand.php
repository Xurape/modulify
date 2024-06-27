<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function Laravel\Prompts\spin;

final class ModulifyListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "modulify:list";

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = "List the current module list";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        spin(fn() => ($modules = $this->getModules()), '-> Loading modules...');

        if (empty($modules)) {
            $this->warn("[!] No modules found. How about creating one? (modulify:make <name>)");
            return;
        }

        $this->table(['Module'], $modules);
    }

    protected function getModules(): array
    {
        $modules = [];

        $directories = File::directories(app_path('Modules'));

        foreach ($directories as $directory) {
            array_push($modules, [Str::after($directory, 'Modules/')]);
        }

        return $modules;
    }
}
