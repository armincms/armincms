<?php

namespace Armincms\Nova\Fields;

/**
 * Class Files
 *
 * @package Ebess\AdvancedNovaMediaLibrary\Fields
 */
class Video extends Images
{  
    protected $defaultValidatorRules = [
    	'mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'
    ];

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  callable|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback); 
    }
}
