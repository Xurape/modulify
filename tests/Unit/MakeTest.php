<?php

use function Pest\Laravel\artisan;

test('make command', function () {
    $testName = 'Test';
    $path = app_path("Modules/{$testName}");

    if(is_dir($path)) {
        $this->fail("Module {$testName} already exists.");
    }

    artisan('modulify:make', ['name' => $testName]);

    expect(app_path(`Modules/{$testName}`))->toBeDirectory();
});
