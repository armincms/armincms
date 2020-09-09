<?php

namespace Armincms\Nova\Fields;


use Illuminate\Support\Str; 
use Laravel\Nova\Http\Requests\NovaRequest;
use Ebess\AdvancedNovaMediaLibrary\Fields\Media as Field;
use Spatie\MediaLibrary\HasMedia\HasMedia;
 

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
 
    protected function handleMedia(NovaRequest $request, $model, $attribute, $data)
    {
        return parent::handleMedia($request, $model, Str::before($attribute, '::'), $data); 
    }

    /**
     * @param HasMedia|HasMediaTrait $resource
     */
    protected function checkCollectionIsMultiple(HasMedia $resource, string $collectionName)
    {
        return parent::checkCollectionIsMultiple($resource, Str::before($collectionName, '::')); 
    }
}
