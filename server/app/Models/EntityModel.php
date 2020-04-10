<?php

namespace App\Models;

use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Ankurk91\Eloquent\BelongsToOne;
use PUGX\Shortid\Shortid;

class EntityModel extends Model
{
    use BelongsToOne;

    /**********************
     * PROPERTIES
     **********************/
    protected $table = 'entities';
    protected $fillable = ['id', 'model', 'content', 'parent_entity', 'published', 'created_by', 'updated_by', 'published_at', 'unpublished_at'];
    protected $casts = [
        'content' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean'
    ];
    protected $contentFields = [
        "title" => [ "multilang" => true ]
    ];

    /**********************
     * SCOPES
     **********************/

    /**
     * Scope a query to only include entities of a given modelId.
     *
     * @param  Builder $query
     * @param  mixed $modelId
     * @return Builder
     */
    public function scopeOfModel($query, $modelId)
    {
        // TODO: Accept array of model ids
        return $query->where('model', $modelId);
    }
    /**
     * Scope a query to only include published entities.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeIsPublished($query)
    {
        return $query->where('is_active', true)
            ->whereDate('published_at', '<=', Carbon::now())
            ->whereDate('unpublished_at', '>', Carbon::now())
            ->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include children of a given parent id.
     *
     * @param Builder $query
     * @param integer $entity_id The id of the parent entity or the short_id
     * @return Builder
     * @throws \Exception
     */
    public function scopeChildOf($query, $entity_id)
    {
        $entity_id = $this->entityIdFromIdOrShortId($entity_id);
        $query->join('relations as relation_children', function ($join) use ($entity_id) {
            $join->on('relation_children.caller_entity_id', '=', 'entities.id')
                ->where('relation_children.called_entity_id', '=', $entity_id)
                ->where('relation_children.depth', '=', 1)
                ->where('relation_children.kind', '=', EntityRelation::RELATION_ANCESTOR)
            ;
        })
        ->addSelect('relation_children.position as position')
        ->addSelect('relation_children.tags as tags');
    }

    /**
     * Scope a query to only include the parent of the given id.
     *
     * @param Builder $query
     * @param number $entity_id The id or short_id of the parent entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeParentOf($query, $entity_id)
    {
        $entity_id = $this->entityIdFromIdOrShortId($entity_id);
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
     * @param Builder $query
     * @param number $entity_id The id or short_id of the parent entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeAncestorOf($query, $entity_id, $order = 'desc')
    {
        $entity_id = $this->entityIdFromIdOrShortId($entity_id);
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
     * @param Builder $query
     * @param number $entity_id The id or short_id of the  entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeDescendantOf($query, $entity_id, $order = 'desc', $depth = NULL)
    {
        $entity_id = $this->entityIdFromIdOrShortId($entity_id);
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
     * @param Builder $query
     * @param number $entity_id The id or short_id of the entity calling the media
     * @return Builder
     * @throws \Exception
     */
    public function scopeMediaOf($query, $entity_id)
    {
        $entity_id = $this->entityIdFromIdOrShortId($entity_id);
        $query->join('relations as relation_media', function ($join) use ($entity_id) {
            $join->on('relation_media.called_entity_id', '=', 'entities.id')
                ->where('relation_media.caller_entity_id', '=', $entity_id)
                ->where('relation_media.kind', '=', EntityRelation::RELATION_MEDIA);
        })
            ->addSelect('relation_media.kind', 'relation_media.position', 'relation_media.depth', 'relation_media.tags')
            ->orderBy('relation_media.position');
    }
    /**
     * Scope a query to flat the languages object.
     *
     * @param  Builder $query
     * @param  string $modelOrFields The id of the model or an array of fields
     * @param  string $lang The lang to use or null to use the default
     * @return Builder
     */

    public function scopeFlatContents($query, $modelOrFields, $lang=null) {
        if (is_string($modelOrFields)) {
            $contentConfig = config("cms.models.$modelOrFields.content", []);
            [$contentFields, $values] = Arr::divide($contentConfig);
        } else {
            $contentFields = $modelOrFields;
        }
        foreach ($contentFields as $field) {
            if ($lang) {
                $query->addSelect("content->".$field."->".$lang." as $field");
            } else {
                $query->addSelect("content->$field as $field");
            }
        }
    }

    /**********************
     * PUBLIC METHODS
     *********************/


    public function addRelation($relationData) {
        if (!isset($relationData['caller_entity_id'])) {
            $relationData['caller_entity_id'] = $this->id;
        }
        self::createRelation($relationData);
    }

    public static function createRelation($relationData) {
        if (!isset($relationData['caller_entity_id'])) {
            throw new InvalidArgumentException('createRelation: caller_entity_id is needed');
        }
        if (!isset($relationData['called_entity_id'])) {
            throw new InvalidArgumentException('createRelation: called_entity_id is needed');
        }
        if (!isset($relationData['kind'])) {
            $relationData['kind'] = EntityRelation::RELATION_UNDEFINED;
        }
        if (!isset($relationData['position'])) {
            $relationData['position'] = 0;
        }
        if (!isset($relationData['depth'])) {
            $relationData['depth'] = 0;
        }
        if (!isset($relationData['tags'])) {
            $relationData['tags'] = [];
        }
        EntityRelation::updateOrCreate(
            [
                "caller_entity_id" => $relationData['caller_entity_id'],
                "called_entity_id" => $relationData['called_entity_id'],
                "kind" => $relationData['kind']
            ],
            [
                "position" => $relationData['position'],
                "depth" => $relationData['depth'],
                "tags" => $relationData['tags']
            ]
        );
    }
    /**
     * Get the content fields associated with the model.
     *
     * @return array
     */
    public function getContentFields()
    {
        return $this->content ?? [];
    }

    /**********************
     * RELATIONS
     **********************/
    public function entitiesRelated($kind = null)
    {
        return $this->belongsToMany('App\Models\Entity', 'relations', 'caller_entity_id', 'called_entity_id')
            ->using('App\Models\EntityRelation')
            ->as('relation')
            ->withPivot('kind', 'position', 'depth', 'tags')
            ->when($kind, function ($q) use ($kind) {
                return $q->where('kind', $kind);
            })
            ->orderBy(EntityRelation::TABLE.'.position')
            ->orderBy(EntityRelation::TABLE.'.created_at')
            ->withTimestamps();
    }
    public function entitiesRelating($kind = null) {
        return $this->belongsToMany('App\Models\Entity', 'relations', 'called_entity_id', 'caller_entity_id')
            ->using('App\Models\EntityRelation')
            ->as('relation')
            ->withPivot('kind', 'position', 'depth', 'tags')
            ->when($kind, function ($q) use ($kind) {
                return $q->where('kind', $kind);
            })
            ->orderBy(EntityRelation::TABLE.'.position')
            ->orderBy(EntityRelation::TABLE.'.created_at')
            ->withTimestamps();
    }
    public function media() {
        return $this->entitiesRelated(EntityRelation::RELATION_MEDIA);
    }
    public function medium() {
        return $this->belongsToOne('App\Models\Medium', 'relations', 'caller_entity_id', 'called_entity_id')
            ->using('App\Models\EntityRelation')
            ->as('relation')
            ->withPivot('kind', 'position', 'depth', 'tags')
            ->where('kind', EntityRelation::RELATION_MEDIA)
            ->orderBy(EntityRelation::TABLE.'.position')
            ->orderBy(EntityRelation::TABLE.'.created_at')
            ->withTimestamps();
    }
    public function routes() {
        return $this->hasMany('App\Models\Route');
    }
    public function route() {
        return $this->hasOne('App\Models\Route')->where('default', true);
    }

    /***********************
     * PRIVATE METHODS
     *********************/

    private function entityIdFromIdOrShortId ($idOrShortId) {
        if (is_numeric($idOrShortId)) {
            return $idOrShortId;
        } elseif (is_string($idOrShortId)) {
            $entity = Entity::select('id')->where('short_id', $idOrShortId)->first();
            if ($entity) {
                return $entity->id;
            } else {
                throw new \Exception('Entity not found by short_id');
            }
        } else {
            throw new \Exception('The id should be and integer or a short_id string');
        }
    }

    /***********************
     * BOOT
     *********************/
    protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $entity) {
            if (!isset($entity['short_id'])) {
                do {
                    $short_id = Shortid::generate(10);
                    $found_duplicate = Entity::where('short_id', $short_id)->first();
                } while (!!$found_duplicate);
                $entity->setAttribute('short_id', $short_id);
            } else {
                $entity->setAttribute('short_id', substr($entity['short_id'], 0, 16));
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
        self::saved(function ($entity) {
            $parentEntity = Entity::with('routes')->find($entity['parent_entity_id']);
            // Create the ancestors relations
            if (isset($entity['parent_entity_id']) && $entity['parent_entity_id'] != NULL && $entity->isDirty('parent_entity_id')) {
                EntityRelation::where("caller_entity_id", $entity->id)->where('kind', EntityRelation::RELATION_ANCESTOR)->delete();
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
            // Create the automatic created routes
            if (isset($entity->content['slug'])) {
                Route::where('entity_id', $entity->id)->where('default', true)->delete();
                foreach ($entity->content['slug'] as $lang => $slug) {
                    if ($parentEntity->routes->count()) {
                        foreach($parentEntity->routes as $route) {
                            if ($route->default && $route->lang === $lang) {
                                $parent_path = $route->path;
                                if ($parent_path === '/') {
                                    $parent_path = '';
                                }
                                Route::create([
                                    "entity_id" => $entity->id,
                                    "entity_model" => $entity->model,
                                    "path" => $parent_path."/".$slug,
                                    "lang" => $lang,
                                    "default" => true
                                ]);
                            }
                        }
                    } else {
                        Route::create([
                            "entity_id" => $entity->id,
                            "entity_model" => $entity->model,
                            "path" => "/".$slug,
                            "lang" => $lang,
                            "default" => true
                        ]);
                    }
                }
            }
        });
    }
}
