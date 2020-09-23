<?php

namespace Armincms;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova as LaravelNova;
use Laravel\Nova\Fields\FieldCollection;
use Armincms\Http\Middleware\Authorize;
use Cviebrock\EloquentSluggable\SluggableObserver;  

class ArmincmsServiceProvider extends ServiceProvider
{
    protected $storageDisks = [
        'image'     => 'images', 
        'video'     => 'videos', 
        'audio'     => 'audios', 
        'document'  => 'documents', 
        'file'      => 'files',
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'armincms');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        $this->mergeConfigurations();
        $this->configureMacros();
        $this->registerPublishing();

        LaravelNova::serving([$this, 'servingNova']);
        \Gate::policy(\Core\User\Models\Admin::class, Policies\AdminPolicy::class);

        $this->app->booted(function () {
            // $this->routes();  
            $this->registerArmincmsStorages(); 

            LaravelNova::serving(function (ServingNova $event) {
                LaravelNova::script(
                    "nova-gutenberg-jquery", __DIR__.'/../resources/js/jquery-1.4.min.js'
                );
            }); 

            \Config::set('nova-policy.migrations', false);
        });     
    }

    public function servingNova()
    {
        LaravelNova::resources([
            Nova\General::class,
            Nova\Admin::class,
            Nova\User::class,
            Nova\Role::class,
        ]);
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
                ->prefix('nova-vendor/armincms')
                ->group(__DIR__.'/../routes/api.php');
    }

    public function mergeConfigurations()
    {  
        Collection::make(File::files(__DIR__.'/../config'))->each(function($file) { 
            $name = File::name($file);

            $this->app['config']->set($name, array_merge(
                $this->app['config']->get($name, []), require $file->getPathname()
            ));
        }); 


        collect([
            'user' => \Core\User\Models\User::class, 
            'admin' => \Core\User\Models\Admin::class
        ])->each(function($class, $guard) {
            Config::set("auth.guards.{$guard}", [
                'driver'    => 'session',
                'provider'  => str_plural($guard), 
            ]);
            Config::set("auth.providers." . str_plural($guard), [
                'driver'=> 'eloquent', 
                'table' => 'users',
                'model' => $class,
            ]); 
            Config::set("auth.passwords." . str_plural($guard), [
                'provider'  => str_plural($guard),
                'table'     => 'password_resets',
                'expire'    => 60,
                'throttle'  => 60,
            ]); 
        }); 
   
        Config::set('laraberg.use_package_routes', false);  
    }

    public function configureMacros()
    {
        /**
         * Filter fields for showing on detail.
         *
         * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
         * @param  mixed  $resource
         * @return \Laravel\Nova\Fields\FieldCollection
         */
        FieldCollection::macro('filterForDetail', function($request, $resource) {
            return $this->filter(function ($field) use ($resource, $request) {
                return $field->isShownOnDetail($request, $resource);
            })->values();
        }); 

        /**
         * Filter fields for showing on detail.
         *
         * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
         * @param  mixed  $resource
         * @return \Laravel\Nova\Fields\FieldCollection
         */
        FieldCollection::macro('filterForIndex', function($request, $resource) {
            return $this->filter(function ($field) use ($resource, $request) {
                return $field->isShownOnIndex($request, $resource);
            })->values();
        });  

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {  
        $this->commands([
            Console\UploadLinkCommand::class,
            Console\PublishCommand::class,
        ]);  

        // should remove after migration to nova
        $this->mergeConfigFrom(
           base_path('vendor/armincms/option/config/option.php'), 'option'
        );

        \Config::set('option.default', 'database');

        $this->app->register(\Core\Armin\ArminServiceProvider::class);

        $this->app->resolving('login', function($manager) {
            $manager
                ->extend(new Admin)
                ->extend(new User);
        });

        $this->app->extend(SluggableObserver::class, function($observer) {   
            return class_exists(\Armincms\Fields\Targomaan::class) 
                        ? new SluggableObserver(new SlugService, $this->app['events'])
                        : $observer;
        }); 
    }

    public function registerArmincmsStorages()
    {
        collect($this->storageDisks)->each(function($path, $name) { 
            $public = config("filesystems.disks.public");
            $public['root'] = storage_path("app/upload/{$path}");
            $public['url']  = url("/upload/{$path}"); 
            $public['visibility']  = 'public'; 

            Config::set("filesystems.disks.armin.{$name}", $public);  
        }); 
    }

    public function registerPublishing()
    {
        if($this->app->runningInConsole()) {  
            $this->publishes([
                __DIR__.'/../resources/views/nova' => resource_path('views/vendor/nova'),
            ], 'armincms-views');
        }
    }
}
