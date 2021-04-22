<?php

namespace Armincms\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;  
use Laravel\Nova\Resource as NovaResource;  
use Laravel\Nova\Http\Requests\NovaRequest;
use Inspheric\NovaDefaultable\HasDefaultableFields;     

abstract class Resource extends NovaResource
{
    use HasLabel, HasDefaultableFields, Queries\PerformsTranslationsQueries, Fields\Helpers; 
   
    /**
     * The columns that should be searched in the translation table.
     *
     * @var array
     */
    public static $searchTranslations = []; 

    /**
     * The relationships that should be eager loaded when performing delete query.
     *
     * @var array
     */ 
    public static $preventDelete = []; 

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return parent::indexQuery($request, static::preventQuery($query));
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
        return parent::detailQuery($request, static::preventQuery($query));
    } 

    /**
     * Build a "prevent" query for the given relation.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function preventQuery($query)
    {  
        collect(static::$preventDelete)->each(function($relation) use ($query) {
            $query->withCount([
                $relation => function($query) {
                    $resource = Nova::resourceForModel($query->getModel());

                    if ($resource::softDeletes()) {
                        $query->withTrashed();
                    } 
                }
            ]);
        });

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

    /**
     * Determine if the current user can delete the given resource or throw an exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeToDelete(Request $request)
    {
        if (! empty(static::$preventDelete) && ! $this->preventDeletion($request)) {
            return false;
        }

        return parent::authorizeToDelete($request);
    }

    /**
     * Determine if the current user can delete the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToDelete(Request $request)
    {
        if (! empty(static::$preventDelete) && ! $this->preventDeletion($request)) {
            return false;
        }

        return parent::authorizedToDelete($request);
    }

    /**
     * Determine if the current user can force delete the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToForceDelete(Request $request)
    {
        if (! empty(static::$preventDelete) && ! $this->preventDeletion($request)) {
            return false;
        }

        return parent::authorizedToForceDelete($request);
    }

    /**
     * Determine if has inUse relation.
     *  
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function preventDeletion($request)    {
        return collect(static::$preventDelete)->filter(function($relation) {
            return data_get($this->resource, $relation.'_count');
        })->isEmpty();
    }
}
