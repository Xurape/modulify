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

    protected $modules = [];
    protected $controllers = [];
    protected $models = [];
    protected $migrations = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if($this->option('module')) {
            $module = $this->option('module');

            if(!File::exists(app_path('Modules/' . $module))) {
                $this->error("-> Module not found.\n");
                return;
            }

            $this->getControllers($module);
            $this->getModels($module);
            $this->getMigrations($module);

            render(<<<"HTML"
                <div class="my-1"> 
                    <span class="font-bold text-green bg-green">Controllers</span> 
                </div>
            HTML);
            $this->table(['Name', 'Path', 'Last modification'], $this->controllers);

            render(<<<"HTML"
            <div class="my-1"> 
                <span class="font-bold text-green bg-green">Models</span> 
            </div>
            HTML);
            $this->table(['Name', 'Path', 'Last modification'], $this->models);
            
            render(<<<"HTML"
            <div class="my-1"> 
                <span class="font-bold text-green bg-green">Database Migrations</span> 
            </div>
            HTML);
            $this->table(['Name', 'Path', 'Last modification'], $this->migrations);
        } else {
            if (empty($this->modules)) {
                if ($this->confirm("-> No modules found. How about creating one?", false)) {
                    $name = $this->ask('-> Enter the module name');
                    $this->call('modulify:make', ['name' => $name]);
                }
            }

            $this->getModules();

            $this->info("\n");
            $this->table(['Name', 'Path', 'Last modification'], $this->modules);
            $this->info("\n");
        }
    }

    protected function getModules()
    {
        $filesystem = new Filesystem();
        $directories = File::directories(app_path('Modules'));

        foreach ($directories as $directory) {
            $name = Str::afterLast($directory, DIRECTORY_SEPARATOR);
            $path = $directory;
            $lastUpdateDate = date('Y-m-d H:i:s', $filesystem->lastModified($directory));
            array_push($this->modules, [$name, $path, $lastUpdateDate]);
        }
    }

    protected function getControllers($module)
    {
        $filesystem = new Filesystem();
        $directories = File::directories(app_path('Modules/' . $module . '/Http/Controllers'));

        foreach ($directories as $directory) {
            $name = Str::afterLast($directory, DIRECTORY_SEPARATOR);
            $path = $directory;
            $lastUpdateDate = date('Y-m-d H:i:s', $filesystem->lastModified($directory));
            array_push($this->controllers, [$name, $path, $lastUpdateDate]);
        }
    }

    protected function getModels($module)
    {
        $filesystem = new Filesystem();
        $directories = File::directories(app_path('Modules/' . $module . '/Models'));

        foreach ($directories as $directory) {
            $name = Str::afterLast($directory, DIRECTORY_SEPARATOR);
            $path = $directory;
            $lastUpdateDate = date('Y-m-d H:i:s', $filesystem->lastModified($directory));
            array_push($this->models, [$name, $path, $lastUpdateDate]);
        }
    }

    protected function getMigrations($module)
    {
        $filesystem = new Filesystem();
        $files = File::files(app_path('Modules/' . $module . '/Database/Migrations'));

        foreach($files as $file) {
            $name = $file->getFilename();
            $path = $file->getPathname();
            $lastUpdateDate = date('Y-m-d H:i:s', $filesystem->lastModified($file->getPathname()));
            array_push($this->migrations, [$name, $path, $lastUpdateDate]);
        }
    }
}
