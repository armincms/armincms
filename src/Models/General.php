<?php 

namespace Armincms\Models;

use Armincms\Bios\Option; 
use Armincms\Targomaan\Concerns\InteractsWithTargomaan; 
use Armincms\Targomaan\Contracts\Translatable; 
 
class General extends Option implements Translatable
{ 
    use InteractsWithTargomaan; 
}
