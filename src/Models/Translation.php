<?php

namespace Armincms\Models; 

use Armincms\Localization\Translation as  Model; 
use Cviebrock\EloquentSluggable\Sluggable;

class Translation extends Model  
{ 
	use Sluggable; 
    
    protected static $sluggable = null;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'seo' => 'json',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        if($sluggable = static::$sluggable) { 
            return [
                'slug' => [
                    'source' => $sluggable
                ]
            ]; 
        }   

        return []; 
    } 

    public static function withSluggable(string $sluggable)
    {
        static::$sluggable = $sluggable;

        return new static;
    }
}