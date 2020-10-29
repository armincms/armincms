<?php

namespace Armincms\Nova\Actions;

use Laravel\Nova\Actions\ActionResource as Resource;
use Armincms\Models\ActionEvent;

class ActionResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = ActionEvent::class; 
}
