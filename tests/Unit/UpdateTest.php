<?php

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

test('update command', function () {
    $output = new BufferedOutput();
    Artisan::call('modulify:update', [], $output);
    $commandOutput = $output->fetch();
    dump($commandOutput);
})->only();
