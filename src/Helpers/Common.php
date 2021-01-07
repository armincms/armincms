<?php 

namespace Armincms\Helpers;

class Common
{ 
    /**
     * Get the resources available for the given interface.
     * 
     * @param  string $class
     * @param  string $interface
     * @return boolean
     */
    public static function instanceOf(string $class, string $interface)
    {
        return collect(class_implements($class))->contains($interface);
    } 
}
