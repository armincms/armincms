<?php 

namespace Armincms\Models;
 
use Laravel\Nova\Actions\ActionEvent as Model;

class ActionEvent extends Model
{ 
    /**
     * Get the user that initiated the action.
     */
    public function user()
    {
        return $this->belongsTo(config('auth.providers.admins.model'), 'user_id');
    }
}