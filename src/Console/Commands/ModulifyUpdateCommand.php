<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Laravel\Prompts\Progress;
use Xurape\Modulify\Providers\ModulifyServiceProvider;

use function Laravel\Prompts\spin;

final class ModulifyUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "modulify:update";

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = "Update modulify ✨";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("\n");



        $progress = new Progress(label: "Updating modulify...", steps: 3);
        $progress->start();
        $progress->hint("Checking for errors...");

        if($this->checkErrors()) {
            return;
        }

        $progress->advance();
        $progress->hint("Updating modulify...");

        $this->updateModulify();

        $progress->advance();
        $progress->hint("Modulify updated successfully!");

        $progress->finish();
    }

    public function checkErrors(): bool
    {
        $currentVersion = ModulifyServiceProvider::getCurrentVersion();
        $latestVersion = '';

        spin(function () use (&$latestVersion) {
            $latestVersion = Http::get('https://api.github.com/repos/xurape/modulify/releases/latest')->json()['tag_name'];
        }, 'Checking for updates');

        if($currentVersion == $latestVersion) {
            $this->warn("-> Modulify is already up to date! Cancelling...");
            return true;
        }

        return false;
    }

    public function updateModulify()
    {
        spin(function () {
            $this->info("-> Updating modulify...");
            shell_exec("composer require xurape/modulify");
        }, 'Updating modulify...');
    }
}
