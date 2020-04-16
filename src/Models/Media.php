<?php 

namespace Armincms\Models;

use Spatie\MediaLibrary\Models\Media as Model;


/**
 * summary
 */
class Media extends Model
{
    /**
     * summary
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function($model) {
        	$dir = dirname($model->getPath());

        	if(! file_exists("{$dir}/index.php")) {
                is_dir($dir) || mkdir($dir, 0777, true);

        		file_put_contents("{$dir}/index.php", '<?php exit("Access is denied!");');
        	}  
        });
    }
}