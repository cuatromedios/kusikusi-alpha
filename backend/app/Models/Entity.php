<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class Entity extends Model
{
    /**********************
     * PROPERTIES
     **********************/
    protected $table = 'entities';
    public $incrementing = false;
    protected $fillable = ['id', 'model', 'content', 'parent_entity', 'published', 'created_by', 'updated_by', 'published_at', 'unpublished_at'];
    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean'
    ];

    /**********************
     * SCOPES
     **********************/

    /**
     * Scope a query to only include entities of a given modelId.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $modelId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfModel($query, $modelId)
    {
        return $query->where('model', $modelId);
    }
    /**
     * Scope a query to only include published entities.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsPublished($query)
    {
        return $query->where('is_active', true)->whereDate('published_at', '<=', Carbon::now())->whereDate('unpublished_at', '>', Carbon::now())->whereNull('deleted_at');
    }
    /**
     * Scope a query to only include children of a given parent id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $entity_id The id of the parent entity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChildOf($query, $parent_entity_id)
    {
        $query->join('relations as relation_children', function ($join) use ($parent_entity_id) {
            $join->on('relation_children.caller_entity_id', '=', 'entities.id')
                ->where('relation_children.called_entity_id', '=', $parent_entity_id)
                ->where('relation_children.depth', '=', 1)
                ->where('relation_children.kind', '=', EntityRelation::RELATION_ANCESTOR)
            ;
        });
    }
    /**
     * Scope a query to only include the parent of the given id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $entity_id The id of the parent entity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParentOf($query, $entity_id)
    {
        $query->join('relations as relation_parent', function ($join) use ($entity_id) {
            $join->on('relation_parent.called_entity_id', '=', 'entities.id')
                ->where('relation_parent.caller_entity_id', '=', $entity_id)
                ->where('relation_parent.depth', '=', 1)
                ->where('relation_parent.kind', '=', EntityRelation::RELATION_ANCESTOR)
            ;
        });
    }
    /**
     * Scope a query to only include ancestors of a given entity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $entity_id The id of the parent entity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAncestorOf($query, $entity_id, $order = 'desc')
    {
        if ($order != 'asc') {
            $order = 'desc';
        }
        $query->join('relations as relation_ancestor', function ($join) use ($entity_id) {
            $join->on('relation_ancestor.called_entity_id', '=', 'entities.id')
                ->where('relation_ancestor.caller_entity_id', '=', $entity_id)
                ->where('relation_ancestor.kind', '=', EntityRelation::RELATION_ANCESTOR)
            ;
        })->orderBy('relation_ancestor.depth', $order);
    }
    /**
     * Scope a query to only include descendants of a given entity id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $entity_id The id of the parent entity
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescendantOf($query, $entity_id, $order = 'desc', $depth = NULL)
    {
        if ($order != 'asc') {
            $order = 'desc';
        }
        if ($depth == NULL) {
            $depth = 9999;
        }
        $query->join('relations as relation_descendants', function ($join) use ($entity_id, $depth) {
            $join->on('relation_descendants.caller_entity_id', '=', 'entities.id')
                ->where('relation_descendants.called_entity_id', '=', $entity_id)
                ->where('relation_descendants.kind', '=', EntityRelation::RELATION_ANCESTOR)
                ->where('relation_descendants.depth', '<=', $depth);
        })->orderBy('relation_descendants.depth', $order)->orderBy('relation_descendants.position');
    }
    /**
     * Scope a query to only get entities being called by another of type medium.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $entity_id The id of the entity calling the relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMediaOf($query, $entity_id)
    {
        $query->join('relations as relation_media', function ($join) use ($entity_id) {
            $join->on('relation_media.called_entity_id', '=', 'entities.id')
                ->where('relation_media.caller_entity_id', '=', $entity_id)
                ->where('relation_media.kind', '=', EntityRelation::RELATION_MEDIA);
        })
            ->addSelect('relation_media.kind', 'relation_media.position', 'relation_media.depth', 'relation_media.tags')
            ->orderBy('relation_media.position');
    }

    /**********************
     * RELATIONS
     **********************/
    public function entitiesRelated()
    {
        return $this->belongsToMany('App\Models\Entity', 'relations', 'caller_entity_id', 'called_entity_id')
            ->using('App\Models\EntityRelation')
            ->as('relation')
            ->withPivot('kind', 'position', 'depth', 'tags')
            ->withTimestamps();
    }
    public function entitiesRelating() {
        return $this->belongsToMany('App\Models\Entity', 'relations', 'called_entity_id', 'caller_entity_id')
            ->using('App\Models\EntityRelation')
            ->as('relation')
            ->withPivot('kind', 'position', 'depth', 'tags')
            ->withTimestamps();
    }

    /***********************
     * BOOT
     *********************/
    protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $entity) {
            // Set the default id as uuid
            if (!isset($entity[$entity->getKeyName()])) {
                $entity->setAttribute($entity->getKeyName(), Str::uuid());
            }
            //Throw an error if not model is defined on create
            if (!isset($entity['model'])) {
                throw new \Exception('A model name is requiered to create a new entity');
            }
            //Set the view as the model name if not view set
            if (!isset($entity['view'])) {
                $entity['view'] = $entity['model'];
            }
            //Set now as the published date if not set
            if (!isset($entity['published_at'])) {
                $entity['published_at'] = Carbon::now();
            }
        });
        self::created(function ($entity) {
            // Create the ancestors relations
            if (isset($entity['parent_entity_id']) && $entity['parent_entity_id'] != NULL) {
                $parentEntity = Entity::findOrFail($entity['parent_entity_id']);
                EntityRelation::create([
                    "caller_entity_id" => $entity->id,
                    "called_entity_id" => $parentEntity->id,
                    "kind" => EntityRelation::RELATION_ANCESTOR,
                    "depth" => 1
                ]);
                $depth = 2;
                foreach ($parentEntity->entitiesRelated as $ancestor) {
                    EntityRelation::create([
                        "caller_entity_id" => $entity->id,
                        "called_entity_id" => $ancestor->id,
                        "kind" => EntityRelation::RELATION_ANCESTOR,
                        "depth" => $depth
                    ]);
                    $depth++;
                }
            };
        });
    }
}
