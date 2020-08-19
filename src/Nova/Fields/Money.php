<?php

namespace Armincms\Nova\Fields;

use Money\Currency; 
use Money\Currencies\ISOCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\AggregateCurrencies;
use Vyuldashev\NovaMoneyField\Money as Field;

/**
 * Class Files
 *
 * @package Ebess\AdvancedNovaMediaLibrary\Fields
 */
class Money extends Field
{   
    public function subUnits(string $currency)
    {
        return (new AggregateCurrencies([
            new ISOCurrencies(),
            new BitcoinCurrencies(),
            new CustomCurrencies()
        ]))->subunitFor(new Currency($currency));
    }
}
