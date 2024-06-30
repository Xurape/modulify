<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Xurape\Modulify\Providers\ModulifyServiceProvider;

use function Laravel\Prompts\spin;
use function Termwind\render;

final class ModulifyVersionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "modulify:version";

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = "Get current modulify version";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentVersion = ModulifyServiceProvider::getCurrentVersion();
        $latestVersion = '';

        spin(function () use (&$latestVersion) {
            $latestVersion = Http::get('https://api.github.com/repos/xurape/modulify/releases/latest')->json()['tag_name'];
        }, 'Checking for updates');

        if($currentVersion == $latestVersion) {
            $updated = 'Yes';
            $color = 'green';
        } elseif (version_compare($currentVersion, $latestVersion, '>')) {
            $updated = 'Yes (Beta)';
            $color = 'green';
        } else {
            $updated = "No";
            $color = 'yellow';
        }

        render(<<<"HTML"
            <div class="mx-2 my-1">
                <div>
                    <span class="font-bold text-green">Package information</span>
 
                    <div class="flex space-x-1">
                        <span class="font-bold">Current version</span>
                        <span class="flex-1 content-repeat-[.] text-gray"></span>
                        <span class="font-bold text-$color">$currentVersion</span>
                    </div>
 
                    <div class="flex space-x-1">
                        <span class="font-bold">Latest version</span>
                        <span class="flex-1 content-repeat-[.] text-gray"></span>
                        <span class="font-bold text-green">$latestVersion</span>
                    </div>
 
                    <div class="flex space-x-1">
                        <span class="font-bold">Updated</span>
                        <span class="flex-1 content-repeat-[.] text-gray"></span>
                        <span class="font-bold text-$color">$updated</span>
                    </div>
                </div>

                <div class="mt-1 text-center w-full">
                    <span class="font-bold text-green">How about giving us a star on <a href="https://github.com/xurape/modulify">Github</a>? 😁🌟</span>
                </div>
            </div>
        HTML);
    }
}
