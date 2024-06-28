<?php

use function Pest\Laravel\artisan;

test('version command', function () {
    artisan('modulify:version');
});