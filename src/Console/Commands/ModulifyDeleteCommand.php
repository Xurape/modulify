<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function Laravel\Prompts\progress;

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

        $this->info("\n");

        $progress = progress(label: "Deleting module {$name}...", steps: 3);
        $progress->start();

        $progress->hint("Checking for errors...");
        
        if($this->checkErrors($name))
        return;
    
        $progress->advance();
        $progress->hint("Confirmation...");

        if(!$this->confirm("[!] Are you sure you want to delete module {$name}?")) {
            $this->warn("\n-> Operation cancelled.");
            $progress->finish();   
            return;
        }

        $progress->advance();
        $progress->hint("Deleting module...");
        $this->deleteModule($name, app_path("Modules/{$name}"));

        $progress->advance();
        $progress->hint("Unregistering module...");
        $this->unregisterModule($name);
        
        $progress->finish();

        $this->info("-> Module {$name} was deleted successfully.\n");
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

        if (!File::exists(app_path("Modules/{$name}"))) {
            $this->info("Module {$name} does not exist.");
            return true;
        }

        if (strpos(File::get(base_path('bootstrap/providers.php')), "App\\Modules\\{$name}\\Providers\\{$name}ServiceProvider::class,") === false) {
            $this->info("Module {$name} is not registered.");
            return true;
        }

        return false;
    }

    protected function deleteModule($name, $path)
    {
        $this->files->deleteDirectory($path);
        File::delete(app_path("Modules/{$name}.php"));
    }

    protected function unregisterModule($name)
    {
        $appConfig = File::get(base_path('bootstrap/providers.php'));
        $appConfig = str_replace("\n    App\\Modules\\{$name}\\Providers\\{$name}ServiceProvider::class,", '', $appConfig);

        File::put(base_path('bootstrap/providers.php'), $appConfig);
    }
}
