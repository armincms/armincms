<?php

namespace Armincms\Nova;
  
use Illuminate\Http\Request;  
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Inspheric\Fields\Url;
use Illuminate\Support\Str;

class General extends ConfigResource
{  

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Armincms\\General'; 

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

            $this->translatable([
                Text::make(__("App Name"), "_app_name_")
                    ->fillUsing(function($request, $model, $attribute, $requestAttribute) {
                        $model->{$attribute} = $request->get($requestAttribute); 
                    })
                    ->onlyOnForms()
                    ->withMeta([
                        'value' => config('app.name')
                    ]),

                Textarea::make(__("App Description"), "_app_description_")
                    ->fillUsing(function($request, $model, $attribute, $requestAttribute) {
                        $model->{$attribute} = $request->get($requestAttribute); 
                    })
                    ->onlyOnForms(), 
            ]),

            Text::make(__("App Name"), function() {
                return static::option("_app_name_::". app()->getLocale());
            }),

            Textarea::make(__("App Description"), function() {
                return static::option("_app_description_::". app()->getLocale());
            }),
        ];
    }
}
