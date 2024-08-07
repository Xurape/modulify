<?php

declare(strict_types=1);

namespace Xurape\Modulify\Providers;

use Illuminate\Support\ServiceProvider;
use Xurape\Modulify\Console\Commands\ModulifyListCommand;
use Xurape\Modulify\Console\Commands\ModulifyMakeCommand;
use Xurape\Modulify\Console\Commands\ModulifyDeleteCommand;
use Xurape\Modulify\Console\Commands\ModulifyUpdateCommand;
use Xurape\Modulify\Console\Commands\ModulifyVersionCommand;
use Xurape\Modulify\Console\Commands\ModulifyDoctorCommand;

final class ModulifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                commands: [
                    ModulifyMakeCommand::class,
                    ModulifyDeleteCommand::class,
                    ModulifyListCommand::class,
                    ModulifyUpdateCommand::class,
                    ModulifyVersionCommand::class,
                    ModulifyDoctorCommand::class,
                ],
            );
        }
    }

    public static function getCurrentVersion(): string
    {
        return json_decode(file_get_contents(__DIR__ . '/../../composer.json'))->version ?? 'latest';
    }
}
