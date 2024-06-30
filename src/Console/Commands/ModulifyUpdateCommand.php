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

        spin(function () {
            $this->info("-> Updating modulify...");
            shell_exec("composer require xurape/modulify > /dev/null 2>&1");
        }, 'Updating modulify...');

        $progress->advance();
        $progress->hint("Modulify updated successfully!");

        $progress->finish();

        $this->info("-> Modulify was successfully updated!\n");
    }

    public function checkErrors(): bool
    {
        $currentVersion = ModulifyServiceProvider::getCurrentVersion();
        $latestVersion = '';

        spin(function () use (&$latestVersion) {
            $latestVersion = Http::get('https://api.github.com/repos/xurape/modulify/releases/latest')->json()['tag_name'];
        }, 'Checking for updates');

        if($currentVersion == $latestVersion) {
            $this->warn("-> Modulify is already up to date! Cancelling...\n");
            return true;
        } else if ($currentVersion > $latestVersion) {
            $this->error("-> Modulify is ahead of the latest version. If you're not using the development version, please report this issue on GitHub.\n");
            return true;
        } else if ($latestVersion == null) {
            $this->error("-> Failed to check for updates. Please try again later.\n");
            return true;
        } else if ($latestVersion == '') {
            $this->error("-> Failed to check for updates. Please try again later.\n");
            return true;
        } else {
            $this->info("-> Modulify is outdated! Current version: {$currentVersion}, Latest version: {$latestVersion}\n");
            return false;
        }
    }
}
