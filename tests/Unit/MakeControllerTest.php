<?php

use function Pest\Laravel\artisan;

test('make controller command', function () {
    $testModuleName = 'Test';
    $controllerName = 'Test';
    $path = app_path("Modules/{$testModuleName}");

    if(!is_dir($path)) {
        $this->fail("Module {$testModuleName} does not exist.");
    }

    if(is_file(app_path("Modules/{$testModuleName}/{$controllerName}"))) {
        $this->fail("Controller {$controllerName} already exists.");
    }

    artisan('modulify:makecontroller', ['module' => $testModuleName, 'name' => $controllerName]);

    expect(app_path(`Modules/{$testModuleName}/{$controllerName}`))->toBeFile();
});
