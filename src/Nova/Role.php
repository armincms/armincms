<?php

namespace Armincms\Nova;
 
use Zareismail\NovaPolicy\Nova\Role as Resource;

class Role extends Resource
{    
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'ACL';
}
