<?php

declare(strict_types=1);

namespace Xurape\Modulify\Providers;

use Illuminate\Support\ServiceProvider;
use Xurape\Modulify\Console\Commands\ModulifyListCommand;
use Xurape\Modulify\Console\Commands\ModulifyMakeCommand;
use Xurape\Modulify\Console\Commands\ModulifyDeleteCommand;

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
                ],
            );
        }
    }
}
