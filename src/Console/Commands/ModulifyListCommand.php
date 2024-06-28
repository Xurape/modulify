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

    protected $modules = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->getModules();
        
        if (empty($this->modules)) {
            if ($this->confirm("-> No modules found. How about creating one?", false)) {
                $name = $this->ask('-> Enter the module name');
                $this->call('modulify:make', ['name' => $name]);
            }
            
            return;
        }

        $this->info("\n");
        $this->table(['Name', 'Path', 'Last modification'], $this->modules);
        $this->info("\n");
    }

    protected function getModules()
    {
        $filesystem = new Filesystem();
        $directories = File::directories(app_path('Modules'));

        foreach ($directories as $directory) {
            $name = Str::afterLast($directory, DIRECTORY_SEPARATOR);
            $path = $directory;
            $creationDate = date('Y-m-d H:i:s', $filesystem->lastModified($directory));
            array_push($this->modules, [$name, $path, $creationDate]);
        }
    }
}
