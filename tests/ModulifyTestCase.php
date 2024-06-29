<?php

declare(strict_types=1);

namespace Xurape\Modulify\Tests;

use Xurape\Modulify\Providers\ModulifyServiceProvider;
use Orchestra\Testbench\TestCase;

class ModulifyTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ModulifyServiceProvider::class,
        ];
    }
}
