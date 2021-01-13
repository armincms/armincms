<?php 

namespace Armincms\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Nova;

class SharedResource
{ 
    /**
     * Get the resources available for the given interface.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $interface
     * @return \Illuminate\Support\Collection
     */
    public static function availableResources(Request $request, string $interface)
    {
        return collect(Nova::$resources)->filter(function($resource) use ($interface) {
            return  Common::instanceOf($resource::$model, $interface) || 
                    is_subclass_of($resource::$model, $interface) ||
                    $resource::$model === $interface;
        })->values();
    }

    /**
     * Get meta data information about all resources for client side consumption.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $interface
     * @return \Illuminate\Support\Collection
     */
    public static function resourceInformation(Request $request, string $interface)
    {
        return static::availableResources($request, $interface)->map(function($resource) {
            return [
                'label' => $resource::label(), 
                'key'   => $resource::uriKey(), 
                'model' => $resource::$model, 
                'relation' => Str::plural(Str::camel(class_basename($resource::$model))), 
            ];
        });
    }
}
