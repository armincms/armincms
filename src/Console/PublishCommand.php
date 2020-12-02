<?php

namespace Armincms\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'armincms:publish {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all of the Armincms resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('nova:publish', [ 
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [ 
            '--force' => $this->option('force'),
            '--provider' => 'Emilianotisato\NovaTinyMCE\FieldServiceProvider',

        ]);  

        $this->call('vendor:publish', [
            '--tag' => 'armincms-views',
            '--force' => $this->option('force'),
        ]);

        $this->call('optimize:clear'); 
    }
}
