<?php 

namespace Armincms;

use Cviebrock\EloquentSluggable\Services\SlugService as Service; 
use Illuminate\Support\{Collection, Str};

/**
 * Class SlugService
 *
 * @package Cviebrock\EloquentSluggable\Services
 */
class SlugService extends Service
{ 
    /**
     * Get all existing slugs that are similar to the given slug.
     *
     * @param string $slug
     * @param string $attribute
     * @param array $config
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getExistingSlugs(string $slug, string $attribute, array $config): Collection
    { 
        $includeTrashed = $config['includeTrashed'];

        $query = $this->model->newQuery()
            ->findSimilarSlugs($attribute, $config, $slug);

        // use the model scope to find similar slugs
        if (method_exists($this->model, 'scopeWithUniqueSlugConstraints')) {
            $query->withUniqueSlugConstraints($this->model, $attribute, $config, $slug);
        }

        // include trashed models if required
        if ($includeTrashed && $this->usesSoftDeleting()) {
            $query->withTrashed();
        }

        // get the list of all matching slugs
        $results = $query->select([
                Str::before($attribute, $this->model::delimiter()), 
                $this->model->getQualifiedKeyName()
            ])
            ->get()
            ->toBase();

        // key the results and return
        return $results->pluck($attribute, $this->model->getKeyName());
    }
}
