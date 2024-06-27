<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class ModulifyDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "modulify:delete {name : The module name}";

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = "Delete a new module";

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
        $name = Str::studly($this->argument('name'));

        if($this->checkErrors($name))
            return;

        $this->info("Deleting module {$name}...");

        $this->deleteModule($name, app_path("Modules/{$name}"));
        $this->unregisterModule($name);
        
        $this->info("Module {$name} was deleted successfully.");
    }

    protected function checkErrors($name): bool
    {
        if(empty($name)) {
            $this->error('Module name is required.');
            return true;
        }

        if (!preg_match('/^[a-zA-Z_]+$/', $name)) {
            $this->error('Module name should only contain letters and underscores.');
            return true;
        }

        if($name === 'Module') {
            $this->error("Module name cannot be 'Module'.");
            return true;
        }

        if(!$this->files->isDirectory(app_path("Modules/{$name}"))) {
            $this->error("Module {$name} does not exist.");
            return true;
        }

        if (!strpos(File::get(config_path('app.php')), "App\\Modules\\{$name}\\Providers\\{$name}ServiceProvider::class,") !== false) {
            $this->info("Module {$name} is not registered.");
            return true;
        }

        return false;
    }

    protected function deleteModule($name, $path)
    {
        $this->files->deleteDirectory($path);
    }

    protected function unregisterModule($name)
    {
        $appConfig = File::get(config_path('app.php'));
        $appConfig = str_replace("App\\Modules\\{$name}\\Providers\\{$name}ServiceProvider::class,", '', $appConfig);

        File::put(config_path('app.php'), $appConfig);
    }
}
