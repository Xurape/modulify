<?php


$is_enabled = false;

if ($is_enabled) {
    test('delete all modules', function () {
        $path = app_path("Modules");
        $modules = scandir($path);

        if(!is_dir($path)) {
            $this->fail("Modules directory does not exist.");
        }

        foreach($modules as $module) {
            if($module === '.' || $module === '..') {
                continue;
            }
            $path = app_path("Modules/{$module}");
            if(is_dir($path)) {
                exec("rm -rf {$path}");
            }
        }

        if(count(scandir(app_path("Modules"))) > 2) {
            $this->fail("Failed to delete all modules.");
        } else {
            expect(app_path("Modules"))->toBeDirectory();
        }
    })->skip();
}
