<?php

use Symfony\Component\Console\Output\BufferedOutput;
use function Pest\Laravel\artisan;

test('list command', function () {
    $output = new BufferedOutput();
    Artisan::call(`modulify:list`, [], $output);
    $commandOutput = $output->fetch();
    dump($commandOutput);
});
