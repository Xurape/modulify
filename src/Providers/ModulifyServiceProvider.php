<?php

declare(strict_types=1);

namespace Xurape\Modulify\Providers;

use Illuminate\Support\ServiceProvider;
use Xurape\Modulify\Console\Commands\ModulifyListCommand;
use Xurape\Modulify\Console\Commands\ModulifyMakeCommand;
use Xurape\Modulify\Console\Commands\ModulifyDeleteCommand;
use Xurape\Modulify\Console\Commands\ModulifyMakeControllerCommand;
use Xurape\Modulify\Console\Commands\ModulifyUpdateCommand;
use Xurape\Modulify\Console\Commands\ModulifyVersionCommand;

final class ModulifyServiceProvider extends ServiceProvider
{
    public static $currentVersion;

    public function boot(): void
    {
        $this->currentVersion = json_decode(file_get_contents(__DIR__ . '/../../composer.json'))->version ?? 'latest';

        if ($this->app->runningInConsole()) {
            $this->commands(
                commands: [
                    ModulifyMakeCommand::class,
                    ModulifyDeleteCommand::class,
                    ModulifyListCommand::class,
                    ModulifyMakeControllerCommand::class,
                    ModulifyUpdateCommand::class,
                    ModulifyVersionCommand::class,
                ],
            );
        }
    }
}
