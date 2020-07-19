<?php 

namespace Armincms\Models;

use Spatie\MediaLibrary\Models\Media as Model;
use Illuminate\Support\Facades\Storage;


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
        	$file = dirname($model->getPath()) . '/index.php';
            $storage = Storage::disk($model->disk);

            $storage->has($file) || Storage::put($file, '<?php exit("Access Denied");');
        });
    }
}