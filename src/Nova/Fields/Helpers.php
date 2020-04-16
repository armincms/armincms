<?php

namespace Armincms\Nova\Fields;

use Armincms\Localization\Fields\Translatable;  
use Armincms\Nova\Admin;
use Armincms\Nova\User; 
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Panel;
use Armincms\Nova\Fields\Video; 
use Armincms\Nova\Fields\Images;
use Armincms\Currency\Nova\Currency;
use GeneaLabs\NovaGutenberg\Gutenberg;
use Vyuldashev\NovaMoneyField\Money;
use OwenMelbz\RadioField\RadioButton;
use Armincms\Json\Json; 
use Armincms\Tab\Tab;
use Superlatif\NovaTagInput\Tags;
use Armincms\RawData\Common;
 

trait Helpers
{ 
    /**
     * Make heading field.
     * 
     * @param  string $name 
     * @return \Laravel\Nova\Fields\Field       
     */
    public function heading(string $name)
    {
        return Heading::make(__($name));
    }

    /**
     * Make panel.
     * 
     * @param  string $name 
     * @return \Laravel\Nova\Fields\Field       
     */
    public function panel(string $name, $fields)
    {
        return new Panel(__($name), $fields);
    }

    /**
     * Make translatable field.
     * 
     * @param  array  $fields 
     * @return \Armincms\Localization\Fields\Translatable         
     */
    public function translatable($fields = [])
    {
        if(is_callable($fields)) {
            $fields = $fields();
        } 

        return Translatable::make(is_array($fields) ? $fields : [$fields]);
    }

    public function tab(callable $builder, string $name = null)
    {
        return Tab::make($name ?? class_basename($this), $builder);
    } 

    public function activeField()
    { 
        return Boolean::make(__("Active"), 'active')->default(false);
    }

    public function tagsInput(string $name, string $attribute)
    {
        return Tags::make(__($name), $attribute);
    }

    public function imageField(string $name = 'Image', string $attribute = 'image')
    {
        return Images::make(__($name), $attribute)
                    ->conversionOnPreview('thumbnail') 
                    ->conversionOnDetailView('thumbnail') 
                    ->conversionOnIndexView('icon')
                    ->fullSize();
        
    }

    public function videoField(string $attribute = 'image', string $name = 'Video')
    {
        return Video::make(__($name), $attribute)
                    ->conversionOnPreview('thumbnail') 
                    ->conversionOnDetailView('thumbnail') 
                    ->conversionOnIndexView('thumbnail')
                    ->fullSize();
        
    }

    public function userField(string $name = 'User', string $relation = 'user')
    {
        return $this->authField($name, $relation);
    } 

    public function authField(string $name = 'User', string $relation = 'user')
    {
        return 
            MorphTo::make(__($name), $relation)->types([
                Admin::class,
                User::class,
            ])->default(request()->user())->readonly(function($request) {
                return ! \Auth::guard('admin')->check() && 
                        $request->user()->can('user.add');
            });
    } 

    public function durationField(string $name = 'Duration', string $attribute = 'duration')
    {
        return $this->jsonField($attribute, [
            Select::make(__("Period"), 'period')
                ->options($durations = Common::durations())
                ->required()
                ->rules('required')
                ->default($durations->keys()->first()),

            Number::make($name, 'count')
                ->required()
                ->rules('required')
                ->default(0),
        ]);
    } 

    public function coordinates()
    {
        return [
            Text::make(__("Latitude"), 'latitude'), 
            
            Text::make(__("Longitude"), 'longitude'), 
        ];
    } 

	/**
	 * Add seo field's.
	 *  
	 * @return \Illuminate\Http\Resources\MergeValue               
	 */
    public function discountField($price = 'price', string $currency="IRR")
    { 
        return $this->merge([
            $this->jsonField('discount', [
                RadioButton::make(__("Discount"), 'type')
                    ->options([
                        "percent"=> __("Percent"),
                        'amount' => __("Amount")
                    ])->toggle([
                        "amount" => ["discount->value"],
                        "percent" => ["discount->amount"], 
                    ])
                    ->default('percent')
                    ->onlyOnForms(),

                Number::make(__("Value"), "value")
                    ->fillUsing(function($request, $attribute, $requestAttribute) {
                        if($request->get("discount->type") === 'amount') {
                            return $request->get("discount->amount");
                        } else {

                            return $request->get("discount->value");
                        }
                    })
                    ->onlyOnForms()
                    ->rules("min:0", "max:99")
                    ->min(0)
                    ->max(99),
            ]),  

            $this->priceField(__("Amount"), "discount->amount")   
                ->fillUsing(function() {})
                ->onlyOnForms(),

            Text::make(__("Discount"), function() use ($price, $currency) {
                $price = is_callable($price) ? $price() : $this->$price; 

                $amount = $this->discount['type'] === 'percent'
                            ? ($price * $this->discount['value']) / 100
                            : $this->discount['value']; 

                $percentage = ceil($price ? ($amount / $price) * 100 : 100); 

                $discountPrice = number_format($amount, 2, '.', ',');

                return "<b>{$discountPrice}</b> [ ~ {$percentage}% ]";
            })
            ->exceptOnForms()
            ->hideFromIndex((Boolean) request('viaResourceId'))
            ->asHtml()
        ]);
    }

    /**
     * Add seo field's.
     *  
     * @return \Illuminate\Http\Resources\MergeValue               
     */
    public function priceField(string $name="Price", string $attribute='price', string $currency="IRR")
    {
        return Money::make(__($name), $currency, $attribute)
                ->default(0.00) 
                ->storedInMinorUnits(); 
    }

    /**
     * Add seo field's.
     *  
     * @return \Illuminate\Http\Resources\MergeValue               
     */
    public function currencyField()
    {
        return BelongsTo::make(__("Currency"), 'currency', Currency::class)
                ->default('IRR') 
                ->rules('required'); 
    }

    /**
     * Add seo field's.
     *  
     * @return \Illuminate\Http\Resources\MergeValue               
     */
    public function seoField()
    {  
    	return 
            KeyValue::make(__('Seo'), 'seo')
                ->resolveUsing(function($value, $resource) { 
                    return array_merge($this->getDefaultSeo(), (array) $value);
                })
                ->keyLabel(__('Name'))
                ->valueLabel(__('Content'))
                ->default($this->getDefaultSeo());
    }

    /**
     * Get default seo meta.
     * 
     * @return array
     */
    public function getDefaultSeo()
    {
        return [
            'title'         => null,
            'description'   => null,
            'robots'        => 'index, follow',
            'keywords'      => null
        ];
    } 

    public function configField($fields = [])
    {
        return Json::make("config", collect($fields)->map(function($fields, $key) {
            return is_array($fields) ? $this->jsonField($key, $fields) : $fields;
        }));
    }

    public function jsonField($name, $fields = [])
    {
        return Json::make($name, $fields);
    } 

    public function slugField()
    {
        return Text::make(__("Slug"), 'slug') 
                    ->nullable()
                    ->hideFromIndex()
                    ->help(__("Caution: cleaning the input causes rebuild it. This string used in url address."));
    }


    /**
     * Add description text field.
     *  
     * @return \Illuminate\Http\Resources\MergeValue               
     */
    public function gutenbergField(string $name = "Description", string $attribute = 'description')
    { 
        return Gutenberg::make(__($name), $attribute)
                    ->nullable()
                    ->hideFromIndex();
    }

    public function abstractField(string $name = "Abstract", string $attribute = 'abstract')
    {
        return Textarea::make(__($name), $attribute)
                    ->rules('max:300')
                    ->nullable()
                    ->hideFromIndex();
    }


    /**
     * Add slug field's.
     *  
     * @return \Illuminate\Http\Resources\MergeValue               
     */
    public function resourceField(string $name = 'Name', string $attribute = 'name')
    {
        return $this->translatable([
            Text::make(__($name), $attribute)
                ->required()
                ->rules('required'),

            $this->slugField(),
        ])->withToolbar();
    }

    public function abstracts(string $name = 'Name', string $attribute = 'name')
    {
        return $this->merge([
            $this->resourceField($name, $attribute),

            $this->translatable($this->abstractField()),
        ]);
    }

    public function descriptions(string $name = 'Name', string $attribute = 'name')
    {
        return $this->merge([ 
            $this->resourceField($name, $attribute),

            $this->translatable([
                $this->abstractField(),

                $this->gutenbergField(), 
            ]), 
        ]);
    }
}
