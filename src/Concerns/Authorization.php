<?php 

namespace Armincms\Concerns;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

trait Authorization
{ 

	public static function bootAuthorization()
	{
		static::saved(function($model) {
			$model->relationLoaded('user') || $model->load('user');
 

			if(! ($model->user instanceof Authenticatable)) {
				$model->user()->associate(request()->user());
				$model->save();
			}
		});
	}

	/**
	 * Indicate Authenticatable.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function user() : MorphTo 
    {
        return $this->morphTo();
    }

    public function scopeAuthenticate($query, Authenticatable $user = null)
    { 
    	return $query
				->where(function($q) use ($user) { 
    				$q
    					->whereUserType(optional($user ?? request()->user())->getMorphClass())
    					->whereUserId(optional($user ?? request()->user())->getKey());
				})
				->orWhere(function($q) { 
    				$q->whereNull('user_type')->whereNull('user_id');
				});
    }
}