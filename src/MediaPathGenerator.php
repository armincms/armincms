<?php

namespace Armincms;

use Spatie\MediaLibrary\PathGenerator\BasePathGenerator;  
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MediaPathGenerator extends BasePathGenerator
{  

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media);
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media);
    }

    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        $collection = Str::kebab($media->collection_name);
        $group  = $this->parseNamespace($media->model_type); 
        $date   = $media->created_at->format('yN/md');// year,week/month,day 

        return "{$group}/{$date}/{$media->model_id}/{$collection}";
    }

    public function parseNamespace(string $namespace)
    {  
        list($path, $model) = array_slice(explode("\\", $namespace), -2);


        $directory = Str::plural(Str::after($model, $path) ?: $path);
 
        return Str::kebab(Str::lower("{$path}/{$directory}")); 
    }
}
