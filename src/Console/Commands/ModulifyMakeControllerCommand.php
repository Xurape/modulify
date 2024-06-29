<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

final class ModulifyMakeControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "modulify:makecontroller {module : The module's name} {name : The controller's name} {--crud : Generate a CRUD controller}";

    /**
     * The aliases of the console command.
     *
     * @var array
     */
    protected $aliases = ['modulify:mc'];

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = "Create a new controller for a module";

    /**
     * The Filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $moduleName = Str::studly($this->argument('module'));
        $controllerName = Str::studly($this->argument('name'));

        $modulePath = app_path("Modules/{$moduleName}");

        $this->info("\n");

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("-> Module {$moduleName} does not exist.");
            return;
        }

        $controllerPath = "{$modulePath}/Http/Controllers/{$controllerName}.php";

        if ($this->files->exists($controllerPath)) {
            $this->error("-> Controller {$controllerName} already exists in module {$moduleName}.");
            return;
        }

        $stubPath = __DIR__ . '/../../stubs/';

        if ($this->option('crud')) {
            $this->files->copy("{$stubPath}/controller_crud.stub", $controllerPath);
        } else {
            $this->files->copy("{$stubPath}/controller.stub", $controllerPath);
        }

        $this->replaceInFile('_NAMESPACE', "App\\Modules\\{$moduleName}\\Http\\Controllers", $controllerPath);
        $this->replaceInFile('_CLASS', "{$controllerName}", $controllerPath);
        $this->replaceInFile('_route', Str::kebab($controllerName), $controllerPath);

        $this->info("-> Controller {$controllerName} created successfully in module {$moduleName}.\n");
    }

    protected function replaceInFile($search, $replace, $file)
    {
        $content = $this->files->get($file);
        $content = str_replace($search, $replace, $content);
        $this->files->put($file, $content);
    }
}
