<?php

namespace Armincms\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Armincms\Fields\BelongsToMany;

class Admin extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Core\\User\\Models\\Admin';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'ACL';


    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'username';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'username', 'email',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make(__('Username'), 'username')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Firstname'), 'firstname')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Lastname'), 'lastname')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Disaply Name'), 'disaplyname')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Email'), 'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:admins,email')
                ->updateRules('unique:admins,email,{{resourceId}}'),

            Boolean::make(__('Active'), 'status')
                ->sortable()
                ->default('activated')
                ->trueValue('activated')
                ->falseValue('pending'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            new Panel(__('Profile'), collect($this->meta)->map(function($value, $key) {
            	return Text::make(__($key), function() use ($value) {return $value;})->onlyOnDetail();
            })),

            new Panel(__('Permissions'), [
                BelongsToMany::make(__('Roles'), 'roles', Role::class)
                    ->fillUsing(function($pivots) {
                        return $pivots;
                    })
                    ->canSee(function($request) {
                        if($request->user()->can('attach', Role::newModel())) {
                            return  ! $request->isUpdateOrUpdateAttachedRequest() ||
                                    ! $request->user()->is($this->resource);
                        }
                    }),
            ]),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ability
     * @return bool
     */
    public function authorizedTo(Request $request, $ability)
    {
        if($this->resource->isDeveloper() && ! $request->user()->is($this->resource)) {
            return false;
        }

        return parent::authorizedTo($request, $ability);
    }
}
