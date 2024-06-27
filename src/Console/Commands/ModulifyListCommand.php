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

    /**
     * The variable to store the files.
     *
     * @var string
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        spin('-> Loading modules...');
        
        $modules = $this->getModules();

        if (empty($modules)) {
            $this->warn("[!] No modules found. How about creating one? (modulify:make <name>)");
            return;
        }

        $this->table(['Module'], $modules);
    }

    protected function getModules(): array
    {
        $modules = [];

        $directories = $this->files->directories(app_path('Modules'));

        foreach ($directories as $directory) {
            $modules[] = [Str::after($directory, 'Modules/')];
        }

        return $modules;
    }
}
