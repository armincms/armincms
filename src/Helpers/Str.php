<?php 

namespace Armincms\Helpers;


class Str
{
    public static function sluggable ($string, $separator) {
	    $slug = mb_strtolower(
	        preg_replace('/([?]|\p{P}|\s)+/u', $separator, $string)
	    );

	    return trim($slug, $separator);
	} 
}
