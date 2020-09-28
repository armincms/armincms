<?php

namespace Armincms\Nova;
 
use Zareismail\NovaPolicy\Nova\Role as Resource;
use Illuminate\Http\Request;

class Role extends Resource
{    
	use HasLabel;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'ACL';

    /**
     * Determine if the current user can view the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ability
     * @return bool
     */
    public function authorizedTo(Request $request, $ability)
    { 
    	if($request->user()->isDeveloper()) {
    		return true;
    	}

    	return with(parent::authorizedTo($request, $ability), function($authorizedTo) use ($request, $ability) {
    		if(! in_array($ability, ['viewAny', 'view']) && $authorizedTo) { 
    			return $request->user()->roles()->whereKey($this->resource->getKey())->count() === 0;
    		}

    		return $authorizedTo;
    	}); 
    }
}
