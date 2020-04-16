<?php

namespace Armincms\Console;

use Illuminate\Console\Command;

class UploadLinkCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'upload:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link from "public/upload" to "storage/app/upload"';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (file_exists(public_path('upload'))) {
            return $this->error('The "public/upload" directory already exists.');
        }

        $this->laravel->make('files')->link(
            storage_path('app/upload'), public_path('upload')
        );

        $this->info('The [public/upload] directory has been linked.');
    }
}
