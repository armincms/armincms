<?php

namespace Armincms\Nova;

use Laravel\Nova\Resource as NovaResource;  
use Laravel\Nova\Http\Requests\NovaRequest;
use Inspheric\NovaDefaultable\HasDefaultableFields;   
use Armincms\Localization\Concerns\PerformsTranslationsQueries; 
use Illuminate\Support\Str;

abstract class Resource extends NovaResource
{
    use HasDefaultableFields, PerformsTranslationsQueries, Fields\Helpers; 
   
    /**
     * The columns that should be searched in the translation table.
     *
     * @var array
     */
    public static $searchTranslations = [];

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'ACL';
    
    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __(static::pluralLabel());
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __(Str::singular(static::pluralLabel()));
    } 

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function pluralLabel()
    {
        return Str::plural(Str::title(Str::snake(class_basename(get_called_class()), ' ')));
    }  

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Scout\Builder  $query
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return parent::relatableQuery($request, $query);
    }
}
