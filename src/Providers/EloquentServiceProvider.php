<?php

namespace Armincms\Providers;
 
use Illuminate\Support\ServiceProvider; 
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Eloquent\Builder; 

class EloquentServiceProvider extends ServiceProvider implements DeferrableProvider
{ 
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {    
    }  

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'db', 'db.connection'
        ];
    }
}
