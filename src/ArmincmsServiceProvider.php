<?php

namespace Armincms;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{File, Route, Config}; 
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova as LaravelNova;
use Cviebrock\EloquentSluggable\SluggableObserver;
use Armincms\Http\Middleware\Authorize;  

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
        $this->registerPublishing();

        LaravelNova::serving([$this, 'servingNova']);
        \Gate::policy(\Core\User\Models\Admin::class, Policies\AdminPolicy::class);

        $this->app->booted(function () {
            // $this->routes();  
            $this->registerArmincmsStorages();  

            \Config::set('nova-policy.migrations', false);
        });     
    }

    public function servingNova()
    {
        LaravelNova::resources([
            Nova\Translation::class,
            Nova\General::class,
            Nova\Admin::class,
            Nova\User::class,
            Nova\Role::class,
        ]);

        LaravelNova::tools([
            \Infinety\Filemanager\FilemanagerTool::make(),
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

        Collection::make(config('general'))->each(function($value, $key) {
            $this->app['config']->set($key, $value);
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
   

        Config::set('option.default', 'database');
        Config::set('laraberg.use_package_routes', false);  
        Config::set('app.url', config('general.url', config('app.url')));  

        $this->app->booted(function() { 
            // this is necessary to add for the meida library url generator
            // if the system locale did not set, the php pathinfo() work incorrect
            // referr here for see example: https://stackoverflow.com/questions/4451664/make-php-pathinfo-return-the-correct-filename-if-the-filename-is-utf-8
            setlocale(LC_ALL, 'en_US');
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

        Config::set("filesystems.disks.upload", array_merge((array) config("filesystems.disks.public"), [
            'root' => storage_path("app/upload"),
            'url' => url("/upload"),
            'visibility' => 'public',
        ])); 

        Config::set("filemanager.disk", 'upload');  
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
