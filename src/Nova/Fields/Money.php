<?php

namespace Armincms\Nova\Fields;
 
use Laravel\Nova\Fields\Currency;
 
class Money extends Currency
{     
    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
    	parent::__construct($name, $attribute, $resolveCallback);
    	$this->setCurrencyStep(); 
    }

    /**
     * Format the field's value into Money format.
     *
     * @param  mixed  $value
     * @param  null|string  $currency
     * @param  null|string  $locale
     *
     * @return string
     */
    public function formatMoney($value, $currency = null, $locale = null)
    { 
    	return currency_format($value, $this->currency);
    } 

    /**
     * Calculate price field step.
     * 
     * @return $this
     */
    public function setCurrencyStep()
    {
    	$format = data_get($this->resolveCurrency(), 'format');

    	preg_match('/\.([0-9]+)/', $format, $matches);

    	$this->step(1/pow(10, strlen($matches[1] ?? 0)));

    	return $this;
    }

    /**
     * Resolve currency data.
     * 
     * @return array
     */
    public function resolveCurrency()
    {
    	return currency()->hasCurrency($this->currency) 
    				? currency()->getCurrency($this->currency) 
    				: array_shift(currency()->getActiveCurrencies()); 
    }

    /**
     * Resolve the symbol used by the currency.
     *
     * @return string
     */
    public function resolveCurrencySymbol()
    {
    	return data_get($this->resolveCurrency(), 'code', $this->currency);
    }
}
