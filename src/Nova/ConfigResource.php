<?php

namespace Armincms\Nova;

use Armincms\Bios\Resource;  
use Illuminate\Http\Request;  
use Inspheric\Fields\Url;

abstract class ConfigResource extends Resource
{ 
    use Fields\Helpers;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = null;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __(static::label());
    } 
}
