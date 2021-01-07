<?php 

namespace Armincms\Contracts;

interface HasLayout
{
	/**
     * Get the single-layouts available for client consumption.
	 * 
	 * @return array
	 */
	public function singleLayouts(): array;

	/**
     * Get the listable-layouts available for client consumption.
	 * 
	 * @return array
	 */
	public function listableLayouts(): array;
}