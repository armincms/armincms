<?php

namespace Armincms\Concerns;

  
trait HasConfig  
{   
	/**
	 * Handle the trait initialization.
	 * 
	 * @return void
	 */
	public function initializeHasConfig()
	{
		$this->casts[$this->getConfigColumn()] = 'array'; 
	} 

    /**
     * Get the config value with the given key.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfig(string $key, $default = null)
    {
    	return data_get($this->config, $key, $default);
    }

    /**
     * Set the config value with the given key.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function setConfig(string $key, $value)
    {
        return $this->fillJsonAttribute($this->getConfigColumn().'->'.$key, $value); 
    }

    /**
     * Get the name of the "config" column.
     *
     * @return string
     */
    public function getConfigColumn()
    {
        return defined('static::CONFIG') ? static::CONFIG : 'config';
    }

    /**
     * Get the fully qualified "config" column.
     *
     * @return string
     */
    public function getQualifiedConfigColumn()
    {
        return $this->qualifyColumn($this->getConfigColumn());
    }
}
