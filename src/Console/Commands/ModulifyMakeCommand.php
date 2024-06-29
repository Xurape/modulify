<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function Laravel\Prompts\progress;

final class ModulifyMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "modulify:make {name : The module's name} {--crud= : Generate a CRUD module}";

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = "Create a new module";

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

        $progress = progress(label: "Creating module {$name}...", steps: 3);
        $progress->start();
        $progress->hint("Checking for errors...");

        if($this->checkErrors($name)) 
            return;

        $progress->advance();
        $progress->hint("Creating module directories...");
        $this->makeModule($name, app_path("Modules/{$name}"));

        $progress->advance();
        $progress->hint("Registering module in providers...");
        $this->registerModule($name);

        $progress->finish();
        
        $this->info("-> Module {$name} was created successfully.\n");
    }

    protected function checkErrors($name): bool
    {
        if(empty($name)) {
            $this->error('-> Module name is required.');
            return true;
        }

        if (!preg_match('/^[a-zA-Z_]+$/', $name)) {
            $this->error('-> Module name should only contain letters and underscores.');
            return true;
        }

        if($name === 'Module') {
            $this->error("-> Module name cannot be 'Module'.");
            return true;
        }

        if($this->files->isDirectory(app_path("Modules/{$name}"))) {
            $this->error("-> Module {$name} already exists.");
            return true;
        }

        if (strpos(File::get(config_path('app.php')), "App\\Modules\\{$name}\\Providers\\{$name}ServiceProvider::class,") !== false) {
            $this->error("-> Module {$name} is already registered.");
            return true;
        }

        return false;
    }

    protected function makeModule($moduleName, $modulePath)
    {
        if(!$this->files->isDirectory(app_path('Modules')))
            $this->files->makeDirectory(app_path('Modules'), 0755, true);
        
        $this->files->makeDirectory($modulePath, 0755, true);
        $this->files->makeDirectory("{$modulePath}/Http/Controllers", 0755, true);
        $this->files->makeDirectory("{$modulePath}/Models", 0755, true);
        $this->files->makeDirectory("{$modulePath}/Routes", 0755, true);
        $this->files->makeDirectory("{$modulePath}/Providers", 0755, true);
        // $this->files->makeDirectory("{$modulePath}/Middlewares", 0755, true); //TODO: Add middlewares
        $this->files->makeDirectory("{$modulePath}/Database/Migrations", 0755, true);
        $this->files->makeDirectory("{$modulePath}/Resources/views", 0755, true);

        $stubPath = __DIR__.'/../../stubs';

        $this->argument('crud') ?
            $this->files->copy("{$stubPath}/controller.crud.stub", "{$modulePath}/Http/Controllers/{$moduleName}Controller.php")
        :
            $this->files->copy("{$stubPath}/controller.stub", "{$modulePath}/Http/Controllers/{$moduleName}Controller.php");

        $this->files->copy("{$stubPath}/serviceprovider.stub", "{$modulePath}/Providers/{$moduleName}ServiceProvider.php");
        $this->files->copy("{$stubPath}/web.stub", "{$modulePath}/Routes/web.php");

        /* Controller */
        $this->replaceInFile('_NAMESPACE', "App\\Modules\\{$moduleName}\\Http\\Controllers", "{$modulePath}/Http/Controllers/{$moduleName}Controller.php");
        $this->replaceInFile('_CLASS', "{$moduleName}Controller", "{$modulePath}/Http/Controllers/{$moduleName}Controller.php");
        $this->replaceInFile('_route', "{$moduleName}", "{$modulePath}/Http/Controllers/{$moduleName}Controller.php");

        /* Service provider */
        $this->replaceInFile('_NAMESPACE', "App\\Modules\\{$moduleName}\\Providers", "{$modulePath}/Providers/{$moduleName}ServiceProvider.php");
        $this->replaceInFile('_CLASS', "{$moduleName}ServiceProvider", "{$modulePath}/Providers/{$moduleName}ServiceProvider.php");
        
        /* Routes */
        $this->replaceInFile('_NAMESPACE', "App\\Modules\\{$moduleName}\\Http\\Controllers", "{$modulePath}/Routes/web.php");
        $this->replaceInFile('_CLASS', "{$moduleName}Controller", "{$modulePath}/Routes/web.php");
        $this->replaceInFile('_route', "{$moduleName}", "{$modulePath}/Routes/web.php");
    }

    protected function registerModule($moduleName)
    {
        $serviceProvider = "App\\Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider::class,";
        
        $appConfigPath = base_path('bootstrap/providers.php');
        $configContent = File::get($appConfigPath);

        $search = "return [";
        $replace = "return [\n    {$serviceProvider}";

        $newConfigContent = str_replace($search, $replace, $configContent);
        File::put($appConfigPath, $newConfigContent);
    }

    protected function replaceInFile($search, $replace, $file)
    {
        $content = $this->files->get($file);
        $content = str_replace($search, $replace, $content);

        $this->files->put($file, $content);
    }
}
