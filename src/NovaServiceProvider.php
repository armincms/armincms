<?php

namespace Armincms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Fields\{BelongsToMany, MorphToMany, FieldCollection};

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        // Nova::routes()
        //         ->withAuthenticationRoutes()
        //         ->withPasswordResetRoutes()
        //         ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new Help,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            (new \Armincms\Tools\ToolbarAction\ToolbarAction),

            (new \Armincms\Bios\Bios)->canSee(function() {
                return \Auth::guard('admin')->check();
            }) 
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configureMacros();
    } 

    public function configureMacros()
    { 
        /**
         * Find a given field by its attribute.
         *
         * @param  string  $attribute
         * @param  mixed  $default
         * @return \Laravel\Nova\Fields\Field|null
         */
        FieldCollection::macro('findFieldByAttribute', function($attribute, $default = null) {
            return $this->first(function ($field) use ($attribute) {
                return isset($field->attribute) &&
                    $field->attribute == $attribute;
            }, $default);
        });

        /**
         * Filter elements should be displayed for the given request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Laravel\Nova\Fields\FieldCollection
         */
        FieldCollection::macro('authorized', function(Request $request) {
            return $this->filter(function ($field) use ($request) {
                return $field->authorize($request);
            })->values();
        });

        /**
         * Filter elements should be displayed for the given request.
         *
         * @param  mixed  $resource
         * @return \Laravel\Nova\Fields\FieldCollection
         */
        FieldCollection::macro('resolve', function($resource) {
            return $this->each(function ($field) use ($resource) {
                if ($field instanceof Resolvable) {
                    $field->resolve($resource);
                }
            });
        });

        /**
         * Resolve value of fields for display.
         *
         * @param  mixed  $resource
         * @return \Laravel\Nova\Fields\FieldCollection
         */
        FieldCollection::macro('resolveForDisplay', function($resource) {
            return $this->each(function ($field) use ($resource) {
                if ($field instanceof Resolvable) {
                    $field->resolveForDisplay($resource);
                }
            });
        });

        /**
         * Filter fields for showing on detail.
         *
         * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
         * @param  mixed  $resource
         * @return \Laravel\Nova\Fields\FieldCollection
         */
        FieldCollection::macro('filterForDetail', function($request, $resource) {
            return $this->filter(function ($field) use ($resource, $request) {
                return $field->isShownOnDetail($request, $resource);
            })->values();
        }); 

        /**
         * Filter fields for showing on detail.
         *
         * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
         * @param  mixed  $resource
         * @return \Laravel\Nova\Fields\FieldCollection
         */
        FieldCollection::macro('filterForIndex', function($request, $resource) {
            return $this->filter(function ($field) use ($resource, $request) {
                return $field->isShownOnIndex($request, $resource);
            })->values();
        }); 

        /**
         * Reject if the field is readonly.
         *
         * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
         * @return \Laravel\Nova\Fields\FieldCollection
         */
        FieldCollection::macro('withoutReadonly', function(NovaRequest $request) {
            return $this->reject(function ($field) use ($request) {
                return $field->isReadonly($request);
            });
        });

        /**
         * Reject fields which use their own index listings.
         *
         * @return \Laravel\Nova\Fields\FieldCollection
         */
        FieldCollection::macro('withoutListableFields', function() {
            return $this->reject(function ($field) {
                return $field instanceof ListableField;
            });
        });

        /**
         * Filter the fields to only many-to-many relationships.
         *
         * @return \Laravel\Nova\Fields\FieldCollection
         */
        FieldCollection::macro('filterForManyToManyRelations', function() {
            return $this->filter(function ($field) {
                return $field instanceof BelongsToMany || $field instanceof MorphToMany;
            });
        }); 

    }

}
