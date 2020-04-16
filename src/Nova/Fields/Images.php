<?php

namespace Armincms\Nova\Fields;

// use Laravel\Nova\Fields\Text;
// use Laravel\Nova\Fields\Trix;

class Images extends Media
{ 

    protected $defaultValidatorRules = ['image'];

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
        //     return "{$originalFilename}.{$extension}";
        // }); 


        $this->croppable();
    }  

    /**
     * Do we deprecate this for SingleMediaRules?
     * @param $singleImageRules
     * @return Images
     */
    public function singleImageRules($singleImageRules): self
    {
        $this->singleMediaRules = $singleImageRules;

        return $this;
    }

    public function croppable(bool $croppable = true): self
    {
        return $this->withMeta(compact('croppable'));
    }

    public function croppingConfigs(array $configs): self
    {
        return $this->withMeta(['croppingConfigs' => $configs]);
    }
}