<?php

namespace Armincms\Concerns; 

use Illuminate\Support\Str;

trait InteractsWithLayouts
{ 
    /**
     * Get the single-layouts available for client consumption.
     * 
     * @return \Illuminate\Support\Collection
     */
    public function singleLayouts() : array
    {
        return layouts(static::singleLayoutKey())->all();
    } 

    /**
     * Get the listable-layouts available for client consumption.
     * 
     * @return \Illuminate\Support\Collection
     */
    public function listableLayouts() : array
    {
        return layouts(static::listableLayoutKey())->all();
    } 

    /**
     * Get the single-layouts group key.
     * 
     * @return string
     */
    public function singleLayoutKey()
    {
        return static::layoutGroupName().'.single';
    } 

    /**
     * Get the listable-layouts group key.
     * 
     * @return string
     */
    public function listableLayoutKey()
    {
        return static::layoutGroupName().'.review';
    } 

    /**
     * Get the layouts group name.
     * 
     * @return string
     */
    public function layoutGroupName() {
        return Str::kebab(class_basename(get_called_class())); 
    }
}
