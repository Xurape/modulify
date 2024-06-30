<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Termwind\render;

final class ModulifyListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "modulify:list {--module= : The module name}";

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = "List the current module list or controller, models, migrations in a module.";

    protected $filesystem;

    public function __construct()
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    public function handle()
    {
        if($this->option('module')) {
            $module = $this->option('module');

            if(!File::isDirectory(app_path('Modules/' . $module))) {
                $this->error("-> Module not found.\n");
                return;
            }

            render(<<<"HTML"
                <div class="my-1">
                    <span class="font-bold text-green bg-green">Controllers</span> 
                </div>
            HTML);
            $this->table(['Name', 'Path', 'Last modification'], $this->getControllers($module));

            render(<<<"HTML"
                <div class="my-1"> 
                    <span class="font-bold text-green bg-green">Models</span> 
                </div>
            HTML);
            $this->table(['Name', 'Path', 'Last modification'], $this->getModels($module));

            render(<<<"HTML"
                <div class="my-1"> 
                    <span class="font-bold text-green bg-green">Database Migrations</span> 
                </div>
            HTML);
            $this->table(['Name', 'Path', 'Last modification'], $this->getMigrations($module));

            $this->info("\n");
        } else {
            $modules = $this->getModules();

            if (empty($modules)) {
                if ($this->confirm("-> No modules found. How about creating one?", false)) {
                    $name = $this->ask('-> Enter the module name');
                    $this->call('modulify:make', ['name' => $name]);
                } else {
                    $this->warn("\n-> No modules found.\n");
                    return;
                }
            }

            $this->info("\n");
            $this->table(['Name', 'Path', 'Last modification'], $modules);
            $this->info("\n");
        }
    }

    protected function getModules(): array
    {
        $directories = File::directories(app_path('Modules'));
        $modules = [];

        foreach ($directories as $directory) {
            $name = Str::afterLast($directory, DIRECTORY_SEPARATOR);
            $path = $directory;
            $lastUpdateDate = date('Y-m-d H:i:s', $this->filesystem->lastModified($directory));
            array_push($modules, [$name, $path, $lastUpdateDate]);
        }

        return $modules;
    }

    protected function getControllers($module): array
    {
        $files = File::files(app_path('Modules/' . $module . '/Http/Controllers'));
        $controllers = [];

        foreach ($files as $file) {
            $name = $file->getFilename();
            $path = $file->getPath();
            $lastUpdateDate = date('Y-m-d H:i:s', $this->filesystem->lastModified($file->getPathname()));
            array_push($controllers, [$name, $path, $lastUpdateDate]);
        }

        return $controllers;
    }

    protected function getModels($module): array
    {
        $files = File::files(app_path('Modules/' . $module . '/Models'));
        $models = [];

        foreach ($files as $file) {
            $name = $file->getFilename();
            $path = $file->getPath();
            $lastUpdateDate = date('Y-m-d H:i:s', $this->filesystem->lastModified($file->getPathname()));
            array_push($models, [$name, $path, $lastUpdateDate]);
        }

        return $models;
    }

    protected function getMigrations($module): array
    {
        $files = File::files(app_path('Modules/' . $module . '/Database/Migrations'));
        $migrations = [];

        foreach ($files as $file) {
            $name = $file->getFilename();
            $path = $file->getPath();
            $lastUpdateDate = date('Y-m-d H:i:s', $this->filesystem->lastModified($file->getPathname()));
            array_push($migrations, [$name, $path, $lastUpdateDate]);
        }

        return $migrations;
    }
}
