<?php

namespace Armincms\Nova\Flexible\Presets;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Heading;
use Whitecube\NovaFlexibleContent\Flexible;
use Whitecube\NovaFlexibleContent\Layouts\Preset;
use Armincms\Nova\Flexible\Layouts\RelatableDisplayFieldsLayout;
use Armincms\Nova\Flexible\Resolvers\RelatableDisplayFieldsResolver;
use Armincms\Helpers\SharedResource;

class RelatableDisplayFields extends Preset
{ 
    /**
     * The Reqeust instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The implementation interface.
     *
     * @var string
     */
    protected $interface; 

    /**
     * Initialize the layout.
     * 
     * @param \Illuminate\Http\Request $request   
     * @param string  $interface 
     */
    public function __construct(Request $request, $interface)
    {
        $this->request  = $request;
        $this->interface= $interface; 
    } 

    /**
     * Execute the preset configuration
     *
     * @return void
     */
    public function handle(Flexible $field)
    {
        // You can call all available methods on the Flexible field.
        // $field->addLayout(...)
        // $field->button(...)
        // $field->resolver(...) 
        SharedResource::availableResources($this->request, $this->interface)->each(function($resource) use ($field) {
            $field->addLayout(new RelatableDisplayFieldsLayout(
                    $resource::label(), $resource::uriKey(), $this->fields($resource)
                ))
                ->resolver(RelatableDisplayFieldsResolver::class)
                ->collapsed();
        });
    }  


    /**
     * Get the fields displayed by the layout.
     *
     * @return array
     */
    public function fields($resource)
    { 
        return  with($this->relatableResourceFields($resource), function($fields) {
            return $fields ?: [
                Heading::make(__('No configuration exists'))
            ];
        });
    }

    /**
     * Returns the categoryable layout fields.
     *       
     * @return array                 
     */
    public function relatableResourceFields($resource)
    {   
        $fieldsCallback = $this->resourceFieldsCallback();

        return method_exists($resource, $fieldsCallback)
                    ? forward_static_call([$resource, $fieldsCallback], $this->request)
                    : [];
    }

    /**
     * Get the relatable fields callback.
     *  
     * @return string           
     */
    public function resourceFieldsCallback()
    { 
        return 'relatable'.class_basename($this->request->model()).'Fields';
    }  
}
