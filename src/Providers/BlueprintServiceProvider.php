<?php

namespace Armincms\Providers;
 
use Illuminate\Support\ServiceProvider; 
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Schema\Blueprint;
use Core\Crud\Statuses;

class BlueprintServiceProvider extends ServiceProvider implements DeferrableProvider
{ 
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {  
        $this->simpleBlueprints();
        $this->complexBlueprints(); 
    } 

    public function simpleBlueprints()
    {
         // Auth blueprint
        Blueprint::macro('auth', function($name = 'user') {
            $this->nullableMorphs($name);

            $this->index(["{$name}_id", "{$name}_type"]);
        });

        Blueprint::macro('dropAuth', function($name = 'user') {
            $this->dropMorphs($name);
        });

         // Price blueprint
        Blueprint::macro('discount', function() {
            $this->json('discount')->nullable()/*default(json_encode([
                'type'  => 'amount', 
                'value' => 0
            ]))*/; 
        });

        Blueprint::macro('dropDiscount', function() {
            $this->dropColumn('discount');
        });

        // Price blueprint
        Blueprint::macro('price', function($name = 'price', $total = 12, $places = 2) {
            $this->double($name, $total, $places)->default(0.00); 
        });

        Blueprint::macro('dropPrice', function($name = 'price') {
            $this->dropColumn($name);
        });

         // small price blueprint
        Blueprint::macro('smallPrice', function($name = 'price') {
            $this->price($name, 10, 2); 
        });

        Blueprint::macro('dropSmallPrice', function($name = 'price') {
            $this->dropColumn($name);
        });

         // long price blueprint
        Blueprint::macro('longPrice', function($name = 'price') {
            $this->price($name, 14, 2); 
        });

        Blueprint::macro('dropLongPrice', function($name = 'price') {
            $this->dropColumn($name);
        });

         // currency blueprint
        Blueprint::macro('currency', function() {
            $this->string('currency', 10); 
        });

        Blueprint::macro('dropCurrency', function() {
            $this->dropColumn('currency');
        });

         // google map blueprint
        Blueprint::macro('coordinates', function() {
            $this->string('latitude')->nullable();
            $this->string('longitude')->nullable();  
        });

        Blueprint::macro('dropCoordinates', function() {
            $this->dropColumn(['latitude', 'longitude']);
        });

        // Duration blueprint
        Blueprint::macro('duration', function() {
            $this->json("duration")->nullable()/*->default(json_encode([
                "period" => "day",
                "count"  => 1
            ]))*/;
        });

        Blueprint::macro('dropDuration', function() {
            $this->dropColumn('duration');
        });

         // Hits blueprint
        Blueprint::macro('hits', function() {
            $this->unsignedBigInteger('hits')->default(0); 
        });

        Blueprint::macro('dropHits', function() {
            $this->dropColumn('hits');
        });

        // Publication blueprint
        Blueprint::macro('publication', function(array $statuses = [], string $default = null, $name = 'status') { 

            $statuses = empty($statuses) ? Statuses::publishing(): $statuses;

            $this->enum($name, $statuses)->default($default ?? head($statuses));
            $this->auth(); 
            $this->softDeletes();
        });

        Blueprint::macro('dropPublication', function($name = 'status') {
            $this->dropColumn($name);
            $this->dropAuth();
            $this->dropSoftDeletes();
        });

        // Schedule blueprint
        Blueprint::macro('scheduling', function(string $default = null) {   
            $this->publication(Statuses::scheduling(), $default ?? Statuses::key('draft'));  
        });

        Blueprint::macro('dropScheduling', function() {
            $this->dropPublication(); 
        });

        // SEO blueprint 
        Blueprint::macro('seo', function() {
            $this->json('seo')->nullable()/*->default(json_encode([
                'description' => null,
                'keywords'  => null,
                'robots'    => 'index,follow',
                'title'     => null,
            ]))*/;
        });

        Blueprint::macro('dropSeo', function() {
            $this->dropColumn('seo');
        });

        // SEO blueprint 
        Blueprint::macro('language', function() {
            $this->string('language', 10)->default('fa');

            $this->index('language');
        });

        Blueprint::macro('dropLanguage', function() {
            $this->dropColumn('language');
        });

        // Slug blueprint 
        Blueprint::macro('slug', function() {
            $this->string('slug')->nullable(); 
        });

        Blueprint::macro('dropSlug', function() {
            $this->dropColumn('slug');
        });

        // Slug blueprint 
        Blueprint::macro('url', function() {
            $this->string('url', 1024)->nullable(); 
        });

        Blueprint::macro('dropUrl', function() {
            $this->dropColumn('url');
        }); 

        // Config blueprint 
        Blueprint::macro('config', function(string $default = '[]') {
            $this->json('config')->nullable()/*->default($default)*/; 
        });

        Blueprint::macro('dropConfig', function() {
            $this->dropColumn('config');
        }); 
    }

    public function complexBlueprints()
    {
         // Permalink blueprint 
        Blueprint::macro('visiting', function() {
            $this->slug();
            $this->url();
            $this->seo();  
        });

        Blueprint::macro('dropVisiting', function() {
            $this->dropSlug();
            $this->dropUrl();
            $this->dropSeo(); 
        }); 

        // Resource blueprint 
        Blueprint::macro('resource', function($name = 'name') {
            $this->string($name)->nullable();
            $this->language(); 
        });

        Blueprint::macro('dropResource', function($name = 'name') {
            $this->dropColumn($name);
            $this->dropLangauge(); 
        });  

        //  Abstract blueprint 
        Blueprint::macro('abstract', function($name = 'name') {
            $this->resource($name);
            $this->visiting(); 
            $this->string('abstract', 500)->nullable();   
        });

        Blueprint::macro('dropAbstract', function($name = 'name') {
            $this->dropColumn('abstract');
            $this->dropResource($name);
            $this->dropVisiting(); 
        });

        //  Context blueprint 
        Blueprint::macro('description', function($name = 'name') {
            $this->abstract($name); 
            $this->longText('description')->nullable();   
        });

        Blueprint::macro('dropDescription', function($name = 'name') { 
            $this->dropColumn('description');
            $this->dropAbstract($name); 
        });  
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'migrator'
        ];
    }
}
