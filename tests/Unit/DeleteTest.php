<?php

use function Pest\Laravel\artisan;

test('delete command', function () {
    $testName = 'Test';
    $path = app_path("Modules/{$testName}");

    if(!is_dir($path)) {
        $this->fail("Module {$testName} does not exist.");
    }

    artisan('modulify:delete', ['name' => $testName]);

    expect(app_path(`Modules/{$testName}`))->toBeDirectory();
});
