<?php

namespace Armincms\Concerns;
  
trait HasDiscount  
{   
	/**
	 * Handle the trait initialization.
	 * 
	 * @return void
	 */
	public function initializeHasDiscount()
	{
		$this->casts[$this->getDiscountColumn()] = 'array'; 
	}

    /**
     * Get the discount percentage for the given amount.
     *  
     * @return float
     */
    public function discountPercent(float $amount)
    { 
    	return  ($amount - $this->applyDiscount($amount)) / $amount * 100; 
    } 

    /**
     * Get the discounted amount.
     *  
     * @return float
     */
    public function discountAmount(float $amount)
    {
        return $this->applyDiscount($amount);
    }

    /**
     * Returns the amount with the discount.
     *  
     * @return float
     */
    public function applyDiscount(float $amount): float
    {
    	return $this->isPercentage() 
    				? $this->applyPercentageDiscount($amount) 
    				: $this->applyAmountDiscount($amount);
    }

    /**
     * Determines if the discount is the percentage.
     * 
     * @return boolean 
     */
    public function isPercentage(): bool
    {
    	return data_get($this->getDiscount(), 'apply', 'percentage') === 'percentage'; 
    }

    /**
     * Applies amount discount to the given amount.
     * 
     * @param  float  $amount 
     * @return float         
     */
    public function applyAmountDiscount(float $amount)
    { 
    	return $amount - $this->getDiscountAmount();
    } 

    /**
     * Returns value of the amount.
     * 
     * @return float
     */
    public function getDiscountAmount(): float
    { 
    	return $this->getDiscountValue(); 
    }

    /**
     * Applies percent discount to the given amount.
     * 
     * @param  float  $amount 
     * @return float         
     */
    public function applyPercentageDiscount(float $amount)
    { 
    	return $amount - ($amount * $this->getDiscountPercentage() / 100);
    } 

    /**
     * Returns value of the percentage.
     * 
     * @return float
     */
    public function getDiscountPercentage(): float
    { 
    	return min($this->getDiscountValue(), 100); 
    } 

    /**
     * Get the discount value.
     * 
     * @return array
     */
    public function getDiscountValue()
    {
    	return floatval($this->getDiscount()['value'] ?? 0);
    }  

    /**
     * Get the discount raw data.
     * 
     * @return array
     */
    public function getDiscount()
    {
    	return (array) $this->getAttribute($this->getDiscountColumn()) ?? [];
    }

    /**
     * Get the name of the "discount" column.
     *
     * @return string
     */
    public function getDiscountColumn()
    {
        return defined('static::DISCOUNT') ? static::DISCOUNT : 'discount';
    }

    /**
     * Get the fully qualified "discount" column.
     *
     * @return string
     */
    public function getQualifiedDiscountColumn()
    {
        return $this->qualifyColumn($this->getDiscountColumn());
    }
}
