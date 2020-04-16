<?php 

namespace Armincms\Config;

use Armincms\Bios\Resource;
use Laravel\Nova\Fields\Text;

/**
 * summary
 */
class General extends Resource
{ 

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
    	return [
    		Text::make(__("App Name"), "name"),
    	];
    }
}
