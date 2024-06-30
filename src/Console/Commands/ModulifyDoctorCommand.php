<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Xurape\Modulify\Providers\ModulifyServiceProvider;

use function Laravel\Prompts\spin;
use function Termwind\render;

final class ModulifyDoctorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "modulify:doctor";

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = "Let's check up with the doctor. Check the package's health status.";

    protected $errors = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $version = null;
        $laravel_version = null;
        $module_errors = null;

        spin(function () use (&$version, &$module_errors, &$laravel_version) {
            $version = $this->checkVersion();
            $laravel_version = $this->checkLaravelVersion();
            $module_errors = $this->checkModules();
        }, 'Checking up the package with the doc...');

        render(<<<"HTML"
            <div class="mx-2 my-1">
                <div>
                    <span class="font-bold text-center mb-1">What's up doc? 🩺</span>
                    <div class="my-1">
                        <span class="font-bold text-green">Laravel</span>
                        <div class="flex space-x-1">
                            <span class="font-bold">Laravel version</span>
                            <span class="flex-1 content-repeat-[.] text-gray"></span>
                            <span class="font-bold text-$laravel_version->color">$laravel_version->currentVersion</span>
                        </div>
     
                        <div class="flex space-x-1">
                            <span class="font-bold">Is it supported?</span>
                            <span class="flex-1 content-repeat-[.] text-gray"></span>
                            <span class="font-bold text-$laravel_version->color">$version->updated</span>
                        </div>
                    </div>
                    <div class="my-1">
                        <span class="font-bold text-green">Version</span>
                        <div class="flex space-x-1">
                            <span class="font-bold">Current version</span>
                            <span class="flex-1 content-repeat-[.] text-gray"></span>
                            <span class="font-bold text-$version->color">$version->currentVersion</span>
                        </div>
     
                        <div class="flex space-x-1">
                            <span class="font-bold">Is it updated?</span>
                            <span class="flex-1 content-repeat-[.] text-gray"></span>
                            <span class="font-bold text-$version->color">$version->updated</span>
                        </div>
                    </div>
                    <div class="my-1">
                        <span class="font-bold text-green">Modules</span>
                        <div class="flex space-x-1">
                            <span class="font-bold">Errors</span>
                            <span class="flex-1 content-repeat-[.] text-gray"></span>
                            <span class="font-bold text-red">$module_errors</span>
                        </div>
                    </div>
                </div>
            </div>
            HTML);

        if(count($this->errors) > 0) {
            $this->error("-> Errors found:");
            $this->table(['Error', 'Solution'], $this->errors);
        } else {
            $this->info("-> No errors found. You're all good! 🎉");
        }

        render(<<<"HTML"
            <div class="mt-1 text-center w-full">
                <span class="font-bold text-green">How about giving us a star on <a href="https://github.com/xurape/modulify">Github</a>? 😁🌟</span>
            </div>
        HTML);
    }

    protected function checkVersion()
    {
        $currentVersion = ModulifyServiceProvider::getCurrentVersion();
        $latestVersion = '';

        spin(function () use (&$latestVersion) {
            $latestVersion = Http::get('https://api.github.com/repos/xurape/modulify/releases/latest')->json()['tag_name'];
        }, 'Checking for updates');

        if($currentVersion == $latestVersion) {
            $updated = 'Yes';
            $color = 'green';
        } elseif (version_compare($currentVersion, $latestVersion, '>')) {
            $updated = 'Yes (Beta)';
            $color = 'green';
        } else {
            $updated = "No";
            $color = 'yellow';
        }

        return (object) [
            'currentVersion' => $currentVersion . ($updated == 'No' ? (` -> $latestVersion`) : ''),
            'updated' => $updated,
            'color' => $color,
        ];
    }

    protected function checkLaravelVersion()
    {
        $currentVersion = app()->version();

        if(version_compare($currentVersion, '11.0.0', '>=')) {
            $supported = 'Yes';
            $color = 'green';
        } else {
            $supported = 'No';
            $color = 'yellow';

            $this->errors[] = [
                "error" => "Laravel version is not supported",
                "solution" => "Upgrade to Laravel 11.x or higher",
            ];
        }

        return (object) [
            'currentVersion' => $currentVersion,
            'supported' => $supported,
            'color' => $color,
        ];
    }

    protected function checkModules(): int
    {
        $errors = 0;
        $directories = File::directories(app_path('Modules'));
        $provider = file_get_contents(base_path('bootstrap/providers.php'));

        foreach ($directories as $directory) {
            $name = Str::afterLast($directory, DIRECTORY_SEPARATOR);
            // $path = $directory; -- Not needed yet

            if(strpos($provider, "App\\Modules\\$name\\Providers\\".$name."ServiceProvider::class") === false) {
                $this->errors[] = [
                    "error" => "Module $name is not registered in providers.php",
                    "solution" => "Add App\\Modules\\$name\\Providers\\".$name."ServiceProvider::class to providers.php",
                ];
                $errors++;
            }
        }

        return $errors;
    }
}
