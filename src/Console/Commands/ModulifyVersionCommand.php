<?php

declare(strict_types=1);

namespace Xurape\Modulify\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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

        spin(fn () => Http::get('https://api.github.com/repos/xurape/modulify/releases/latest')) ->then(function ($response) use (&$latestVersion) { $latestVersion = $response['tag_name']; });

        if($currentVersion == $latestVersion) {
            $updated = true;
            $color = 'green';
        } else {
            $updated = false;
            $color = 'yellow';
        }

        render(<<<"HTML"
            <div class="mx-2 my-1">
                <div class="space-x-1">
                    <span class="px-1 bg-blue-500 text-white">Modulify ✨</span>
                </div>
 
                <div class="mt-1">
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
            </div>
        HTML);
    }
}
