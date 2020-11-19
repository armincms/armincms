<?php

namespace Armincms\Nova;
  
use Illuminate\Http\Request;   
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Inspheric\Fields\Url;
use Armincms\Bios\Resource; 
use Armincms\Fields\Targomaan;

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

            new Targomaan([
                Text::make(__("App Name"), "_app_name_")
                    ->fillUsing(function($request, $model, $attribute, $requestAttribute) {
                        $model->{$attribute} = $request->get($requestAttribute); 
                    })
                    ->withMeta([
                        'value' => config('app.name')
                    ]),

                Textarea::make(__("App Description"), "_app_description_")
                    ->fillUsing(function($request, $model, $attribute, $requestAttribute) {
                        $model->{$attribute} = $request->get($requestAttribute); 
                    }), 
            ]), 
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
            file_put_contents(
                config_path('general.php'), '<?php return ["url"=>"' .static::option('_main_doamin_').'"];'
            );
            \Artisan::call('config:cache');
        });
    }
}
