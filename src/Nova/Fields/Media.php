<?php

namespace Armincms\Nova\Fields;


use Ebess\AdvancedNovaMediaLibrary\Fields\Media as Field;
use Illuminate\Support\Str;
 

class Media extends Field
{
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

        // $this->setFileName(function($originalFilename, $extension, $model) {  
        //     $time = collect(explode(' ', microtime()))->reverse()->implode('.');

        //     return Str::slug($time).".{$extension}";
        // }); 
    } 
}
