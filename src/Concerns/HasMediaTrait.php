<?php 

namespace Armincms\Concerns;

use Illuminate\Support\{Collection, Str};
use Spatie\MediaLibrary\HasMedia\HasMediaTrait as SpatieHasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Image\Manipulations;

trait HasMediaTrait
{ 
	use SpatieHasMediaTrait {
        getMedia as spatieGetMedia;
    }

	// protected $medias = [
 //        'image' => [
 //            'multiple' => true,
 //            'disk'  => 'armin.image',
 //            'conversions' => [
 //                'residence', 'residence.list', '*'
 //            ]
 //        ],
	// ];

    public function registerMediaCollections()
    {
    	Collection::make($this->medias)->each(function($config, $name) { 
            tap($this->addMediaCollection($name), function($collection) use ($config) {
                $collection->useDisk($config['disk'] ?? 'armin.file');
                $collection->useFallbackUrl(schema_placeholder('main'));

                if(! isset($config['multiple']) || $config['multiple'] === false) {
                    $collection->singleFile();
                }

                $collection->registerMediaConversions(function(Media $media) use ($config) { 
                    $this
                        ->schemas($config['conversions'] ?? 'common') 
                        ->each(function($schemas, $conversion) { 
                            $schemas->keyBy(function($schema, $key) use ($conversion) {
                                        return "{$conversion}-{$key}";
                                }) 
                                ->each([$this, 'registerSchemaConversion']);
                            
                        }); 
                });
            });  
    	});
    } 

    public function registerSchemaConversion($schema, $name)
    {      
		$conversion = $this
						->addMediaConversion($name)
						->width($schema['width'] ?? 0)
						->height($schema['height'] ?? 0)
						->quality(100 - ($schema['compress'] ?? 0)) 
                        ->extractVideoFrameAtSecond(1);

        if(isset($schema['extension'])) { 
            $conversion = $conversion->format($schema['extension']); 
        } else {
            $conversion = $conversion->keepOriginalImageFormat();
        } 

        if(isset($schema['background'])) {
            $conversion = $conversion->background($schema['background']);
        }  

        $this
            ->parseManipulations($schema['manipulations'] ?? ['crop' => 'crop-center'])
            ->each(function($position, $manipulation) use ($conversion, $schema) {  
                $conversion->{$manipulation}($position, $schema['width'] ?? 0, $schema['height'] ?? 0);
            }); 
    } 

    public function parseManipulations($manipulations)
    {
        if(is_string($manipulations)) {
            $manipulations = [$manipulations];
        } 

        return collect($manipulations)->mapWithKeys(function($value, $key) {
            if(is_numeric($key)) {
                $key = $value;
                $value = 'center'; 
            }

            return [
                $key => $value
            ];
        });
    }

    /**
     * Get curent schema configurations
     * 
     * @return \Illuminate\Support\Collection
     */
    public function schemas($conversions)
    {   
        return collect((array) $conversions)->filter(function($driver) {
                    return \Conversion::has($driver);
                })
                ->flip() 
                ->map(function($schemas, $conversion) {
                    return collect(\Conversion::driver($conversion)->schemas());
                }); 
    }  

    /**
     * Retrive conversions of an media
     * 
     * @param \Spatie\MediaLibrary\Models\Media $media      
     * @param  array $conversions
     * @return \Illuminate\Support\Collection             
     */
    public function getConversions(Media $media = null, array $conversions)
    { 
        $conversions = array_combine($conversions, $conversions);

        return collect($conversions)->map(function($conversion) use ($media) { 
            if(optional($media)->hasGeneratedConversion($conversion)) {
                return $media->getFullUrl($conversion);
            }

            return schema_placeholder($conversion); 
        });
    }

    /**
     * Get media collection by its collectionName.
     *
     * @param string $collectionName
     * @param array|callable $filters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMedia(string $collectionName = 'default', $filters = []): Collection
    {
        return $this->spatieGetMedia(Str::before($collectionName, '::'), $filters);
    }
}