<?php 

namespace Armincms\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Authorizable
{
	/**
	 * Indicate Authenticatable.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function user() : MorphTo; 
}