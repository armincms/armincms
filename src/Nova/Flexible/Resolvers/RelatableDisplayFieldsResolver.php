<?php

namespace Armincms\Nova\Flexible\Resolvers;

use Whitecube\NovaFlexibleContent\Value\ResolverInterface;

class RelatableDisplayFieldsResolver implements ResolverInterface
{

    /**
     * get the field's value
     *
     * @param  mixed  $resource
     * @param  string $attribute
     * @param  Whitecube\NovaFlexibleContent\Layouts\Collection $layouts
     * @return Illuminate\Support\Collection
     */
    public function get($resource, $attribute, $layouts)
    { 
        return $layouts->map(function($layout) use ($resource) { 
            $key = $layout->name(); 

            if($attributes = (array) $resource->getConfig("relatable.{$key}")) { 
                return $layout->duplicateAndHydrate($layout->name(), $attributes);
            };  
        })->filter()->values(); 
    }

    /**
     * Set the field's value
     *
     * @param  mixed  $model
     * @param  string $attribute
     * @param  Illuminate\Support\Collection $groups
     * @return string
     */
    public function set($model, $attribute, $groups)
    {
        $value = $groups->filter()->mapWithKeys(function($group) {
            $attributes = collect($group->getAttributes())->map(function($value) {
                if(is_string($value) && $array = json_decode($value, true)) {
                    return $array;
                } 

                return $value; 
            });

            return [$group->name() => $attributes];
        });

        $model->fillJsonAttribute('config->relatable', $value->toArray());
    }
}
