<?php 

namespace Armincms;

use Armincms\Bios\Option; 
use Armincms\Localization\Concerns\HasTranslation; 
use Armincms\Localization\Contracts\Translatable; 
 
class General extends Option implements Translatable
{ 
    use HasTranslation;
}
