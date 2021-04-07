<?php

namespace Armincms\Nova;
  
use Illuminate\Http\Request;   
use Laravel\Nova\Fields\{Text, Textarea, Timezone, Select}; 
use Laravel\Nova\Http\Requests\NovaRequest;
use Inspheric\Fields\Url;
use Armincms\Bios\Resource; 
use Superlatif\NovaTagInput\Tags;

class General extends Resource
{   
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = null;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Armincms\Models\General::class; 

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __("General");
    }
 
    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Timezone::make(__('Application Timezone'), 'timezone')
                ->required()
                ->rules('required')
                ->withMeta([
                    'value' => static::option('timezone', config('app.timezone')),
                ]),

            Select::make(__('Default Currency'), 'default_currency')
                ->options(collect(currency()->getActiveCurrencies())->pluck('symbol', 'code'))
                ->withMeta([
                    'value' => static::option('currency', 'IRR'),
                ]),

            Url::make(__("Main Domain"), "_main_doamin_")
                ->rules('url')
                ->withMeta([
                    'value' => config('app.url')
                ])
                ->alwaysClickable()
                ->title(config('app.name'))
                ->label(config('app.name')),

            Url::make(__("Api Domain"), "_api_doamin_")
                ->rules('url')
                ->withMeta([
                    'value' => config('app.url').'/api'
                ]) 
                ->nameLabel()
                ->alwaysClickable(), 

            Text::make(__("App Title"), "_app_name_")
                ->fillUsing(function($request, $model, $attribute, $requestAttribute) {
                    $model->{$attribute} = $request->get($requestAttribute); 
                })
                ->withMeta([
                    'value' => config('app.name')
                ]),

            Text::make(__("Home Title"), "_app_title_")
                ->fillUsing(function($request, $model, $attribute, $requestAttribute) {
                    $model->{$attribute} = $request->get($requestAttribute); 
                }),

            Textarea::make(__("App Description"), "_app_description_")
                ->fillUsing(function($request, $model, $attribute, $requestAttribute) {
                    $model->{$attribute} = $request->get($requestAttribute); 
                }), 

            Tags::make(__("App Tags"), "_app_tags_")
                ->fillUsing(function($request, $model, $attribute, $requestAttribute) {
                    $model->{$attribute} = collect(json_decode($request->get($requestAttribute), true))->pluck('text')->values()->implode(','); 
                })
                ->resolveUsing(function($value) {
                    return array_filter(explode(',', $value)); 
                }),  
        ];
    }

    /**
     * Return the location to redirect the user after update.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return tap(parent::redirectAfterUpdate($request, $resource), function() {
            ob_start();
            var_export(array_filter([
                'app.name' => static::option('_app_name_', 'Armin CMS'),
                'app.url' => static::option('_main_doamin_'),
                'app.timezone' => static::option('timezone'),
                'nova.currency' => static::option('default_currency', 'IRR'),
            ]));
            $options = ob_get_clean();

            file_put_contents(
                config_path('general.php'), '<?php return '.$options.';'
            );
            \Artisan::call('config:cache');
        });
    }
}
