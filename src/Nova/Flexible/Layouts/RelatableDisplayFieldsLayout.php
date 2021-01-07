<?php

namespace Armincms\Nova\Flexible\Layouts;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Heading;
use Whitecube\NovaFlexibleContent\Layouts\Layout;

class RelatableDisplayFieldsLayout extends Layout
{   
    /**
     * Decode the given JSON back into an array or object.
     *
     * @param  string  $value
     * @param  bool  $asObject
     * @return mixed
     */
    public function fromJson($value, $asObject = false)
    {  
        return parent::fromJson(is_string($value) ? $value : json_encode($value), $asObject);
    }
}
