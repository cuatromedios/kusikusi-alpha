<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Ankurk91\Eloquent\BelongsToOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PUGX\Shortid\Shortid;
use App\Models\Traits\UsesShortId;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntityModel extends Model
{
    use BelongsToOne;
    use UsesShortId;
    use SoftDeletes;

    /**********************
     * PROPERTIES
     **********************/

    protected $table = 'entities';
    protected $fillable = ['id', 'model', 'properties', 'view', 'parent_entity_id', 'is_active', 'published_at', 'unpublished_at', 'contents', 'relations'];
    protected $guarded = ['id'];
    protected $contentFields = [ "title", 'slug' ];
    protected $propertiesFields = [];
    private $storedContents = [];
    private $storedRelations = [];

    /**
     * @var array A list of columns from the entities tables and other joins needs to be casted
     */
    protected $casts = [
        'properties' => 'array',
        'tags' => 'array',
        'child_relation_tags' => 'array',
        'descendant_relation_tags' => 'array',
        'siblings_relation_tags' => 'array',
        'media_tags' => 'array',
        'relation_tags' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * ACCESORS AND MUTATORS
     */

    public function getPublishedAtAttribute($value)
    {
        return isset($value) ? Carbon::make($value)->format('Y-m-d\TH:i:sP') : null;
    }
    public function getUnpublishedAtAttribute($value)
    {
        return isset($value) ? Carbon::make($value)->format('Y-m-d\TH:i:sP') : null;
    }
    public function getCreatedAtAttribute($value)
    {
        return isset($value) ? Carbon::make($value)->format('Y-m-d\TH:i:sP') : null;
    }
    public function getUpdatedAtAttribute($value)
    {
        return isset($value) ? Carbon::make($value)->format('Y-m-d\TH:i:sP') : null;
    }
    public function setPublishedAtAttribute($value)
    {
        if ($value !== null) $this->attributes['published_at'] = Carbon::make($value)->setTimezone('UTC')->format('Y-m-d\TH:i:s');
    }
    public function setUnpublishedAtAttribute($value)
    {
        if ($value !== null) $this->attributes['unpublished_at'] = Carbon::make($value)->setTimezone('UTC')->format('Y-m-d\TH:i:s');
    }

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
            ->whereDate('published_at', '<=', Carbon::now()->setTimezone('UTC')->format('Y-m-d\TH:i:s'))
            ->where(function($query) {
                $query->whereDate('unpublished_at', '>', Carbon::now()->setTimezone('UTC')->format('Y-m-d\TH:i:s'))
                    ->orWhereNull('unpublished_at');
            })
            ->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include children of a given parent id.
     *
     * @param Builder $query
     * @param integer $entity_id The id of the parent entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeChildOf($query, $entity_id)
    {
        $query->join('relations as relation_children', function ($join) use ($entity_id) {
            $join->on('relation_children.caller_entity_id', '=', 'entities.id')
                ->where('relation_children.called_entity_id', '=', $entity_id)
                ->where('relation_children.depth', '=', 1)
                ->where('relation_children.kind', '=', EntityRelation::RELATION_ANCESTOR)
            ;
        })
            ->addSelect('relation_children.position as child_relation_position')
            ->addSelect('relation_children.tags as child_relation_tags');
    }

    /**
     * Scope a query to only include the parent of the given id.
     *
     * @param Builder $query
     * @param number $entity_id The id of the parent entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeParentOf($query, $entity_id)
    {
        $query->join('relations as relation_parent', function ($join) use ($entity_id) {
            $join->on('relation_parent.called_entity_id', '=', 'entities.id')
                ->where('relation_parent.caller_entity_id', '=', $entity_id)
                ->where('relation_parent.depth', '=', 1)
                ->where('relation_parent.kind', '=', EntityRelation::RELATION_ANCESTOR)
            ;
        })
            ->addSelect('relation_parent.depth as parent_relation_depth');
    }

    /**
     * Scope a query to only include ancestors of a given entity.
     *
     * @param Builder $query
     * @param number $entity_id The id of the parent entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeAncestorOf($query, $entity_id)
    {
        $query->join('relations as relation_ancestor', function ($join) use ($entity_id) {
            $join->on('relation_ancestor.called_entity_id', '=', 'entities.id')
                ->where('relation_ancestor.caller_entity_id', '=', $entity_id)
                ->where('relation_ancestor.kind', '=', EntityRelation::RELATION_ANCESTOR)
            ;
        })
            ->addSelect('relation_ancestor.depth as ancestor_relation_depth');
    }

    /**
     * Scope a query to only include descendants of a given entity id.
     *
     * @param Builder $query
     * @param number $entity_id The id of the  entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeDescendantOf($query, $entity_id, $depth = 99)
    {
        $query->join('relations as relation_descendants', function ($join) use ($entity_id, $depth) {
            $join->on('relation_descendants.caller_entity_id', '=', 'entities.id')
                ->where('relation_descendants.called_entity_id', '=', $entity_id)
                ->where('relation_descendants.kind', '=', EntityRelation::RELATION_ANCESTOR)
                ->where('relation_descendants.depth', '<=', $depth);
        })
            ->addSelect('relation_descendants.position as descendant_relation_position')
            ->addSelect('relation_descendants.depth as descendant_relation_depth')
            ->addSelect('relation_descendants.tags as descendant_relation_tags');
    }

    /**
     * Scope a query to only include descendants of a given entity id.
     *
     * @param Builder $query
     * @param number $entity_id The id of the  entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeSiblingsOf($query, $entity_id)
    {
        $parent_entity = Entity::find($entity_id);
        $query->join('relations as relation_siblings', function ($join) use ($parent_entity) {
            $join->on('relation_siblings.caller_entity_id', '=', 'entities.id')
                ->where('relation_siblings.called_entity_id', '=', $parent_entity->parent_entity_id)
                ->where('relation_siblings.depth', '=', 1)
                ->where('relation_siblings.kind', '=', EntityRelation::RELATION_ANCESTOR)
            ;
        })
            ->where('entities.id', '!=', $entity_id)
            ->addSelect('relation_siblings.position as siblings_relation_position')
            ->addSelect('relation_siblings.tags as siblings_relation_tags');
    }

    /**
     * Scope a query to only get entities being called by.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $entity_id The id of the entity calling the relations
     * @param  string $kind Filter by type of relation, if ommited all relations but 'ancestor' will be included
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRelatedBy($query, $entity_id, $kind = null)
    {
        $query->join('relations as related_by', function ($join) use ($entity_id, $kind) {
            $join->on('related_by.called_entity_id', '=', 'entities.id')
                ->where('related_by.caller_entity_id', '=', $entity_id);
            if ($kind === null) {
                $join->where('related_by.kind', '!=', 'ancestor');
            } else {
                $join->where('related_by.kind', '=', $kind);
            }
        })->addSelect('related_by.kind as relation_kind', 'related_by.position as relation_position', 'related_by.depth as relation_depth', 'related_by.tags as relation_tags');
    }

    /**
     * Scope a query to only get entities calling.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $entity_id The id of the entity calling the relations
     * @param string $kind Filter by type of relation, if ommited all relations but 'ancestor' will be included
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    public function scopeRelating($query, $entity_id, $kind = null)
    {
        $query->join('relations as relating', function ($join) use ($entity_id, $kind) {
            $join->on('relating.caller_entity_id', '=', 'entities.id')
                ->where('relating.called_entity_id', '=', $entity_id);
            if ($kind === null) {
                $join->where('relating.kind', '!=', 'ancestor');
            } else {
                $join->where('relating.kind', '=', $kind);
            }
        })->addSelect('relating.kind as relation_kind', 'relating.position as relation_position', 'relating.depth as relation_depth', 'relating.tags as relation_tags');
    }

    /**
     * Scope a query to only get entities being called by another of type medium.
     *
     * @param Builder $query
     * @param number $entity_id The id of the entity calling the media
     * @return Builder
     * @throws \Exception
     */
    public function scopeMediaOf($query, $entity_id)
    {
        $query->join('relations as relation_media', function ($join) use ($entity_id) {
            $join->on('relation_media.called_entity_id', '=', 'entities.id')
                ->where('relation_media.caller_entity_id', '=', $entity_id)
                ->where('relation_media.kind', '=', EntityRelation::RELATION_MEDIA);
        })
            ->addSelect( 'relation_media.position as media_position', 'relation_media.depth as media_depth', 'relation_media.tags as media_tags');
    }
    /**
     * Scope a query to flat the properties json column.
     *
     * @param  Builder $query
     * @param  string $modelOrFields The id of the model or an array of fields
     * @return Builder
     */

    public function scopeAppendProperties($query, $modelOrFields=null) {
        if (is_string($modelOrFields)) {
            $modelClassName = "App\\Models\\" . Str::studly($modelOrFields);
            if (class_exists($modelClassName)) {
                $modelInstance = new $modelClassName();
                $propertiesFields = $modelInstance->getPropertiesFields();
            } else {
                $propertiesFields = [];
            }
        } else if (is_array($modelOrFields)) {
            $propertiesFields = $modelOrFields;
        } else {
            $propertiesFields = $modelOrFields;
        }
        foreach ($propertiesFields as $field) {
            $query->addSelect("properties->$field as $field");
        }
    }
    /**
     * Scope a query to flat the contents.
     *
     * @param  Builder $query
     * @param  string $modelOrFields The id of the model or an array of fields
     * @param  string $lang The lang to use or null to use the default
     * @return Builder
     */

    public function scopeAppendContents($query, $modelOrFields=null, $lang=null) {
        $lang = $lang ?? Config::get('cms.langs')[0] ?? '';
        if (is_string($modelOrFields)) {
            $modelClassName = "App\\Models\\".Str::studly($modelOrFields);
            if (class_exists($modelClassName)) {
                $modelInstance =  new $modelClassName();
                $contentFields = $modelInstance->getContentFields();
            } else {
                $contentFields = [];
            }

        } else if (is_array($modelOrFields)) {
            $contentFields = $modelOrFields;
        } else {
            $contentFields = $this->contentFields;
        }
        foreach ($contentFields as $field) {
            $rand = rand(10000, 99999);
            $query->leftJoin("contents as content_{$rand}_{$field}", function ($join) use ($field, $lang, $rand) {
                $join->on("content_{$rand}_{$field}.entity_id", "entities.id")
                    ->where("content_{$rand}_{$field}.field", $field)
                    ->where("content_{$rand}_{$field}.lang", $lang)
                ;
            });
            $query->addSelect("content_{$rand}_{$field}.text as $field");
        }
    }
    /**
     * Scope a query to flat the route.
     *
     * @param  Builder $query
     * @param  string $modelOrFields The id of the model or an array of fields
     * @param  string $lang The lang to use or null to use the default
     * @return Builder
     */

    public function scopeAppendRoute($query, $lang=null) {
        $lang = $lang ?? Config::get('cms.langs')[0] ?? '';
        $query->leftJoin("routes", function ($join) use ($lang) {
            $join->on("routes.entity_id", "entities.id")
                ->where("routes.default", 1)
                ->where("routes.lang", $lang)
            ;
        });
        $query->addSelect("routes.path as route");
    }

    /**
     * Scope to append a medium url to the result.
     *
     * @param  Builder $query
     * @param  string $tag Select the first related media that is tagged, the first medium if ommitted
     * @param  string $fields An array of fields to include, ['id', 'properties->format', 'contents.title'] if omitted
     * @return Builder
     */

    public function scopeAppendMedium($query, $tag=null, $fields=null, $lang=null) {
        $query->with(['medium' => function ($relation) use ($fields, $lang, $tag) {
            $relation->select('id', 'properties->format as format');
            if (isset($tag)) {
                $relation->whereJsonContains('tags', $tag);
            }
            $mediumContentFields = (new Medium())->getContentFields();
            $addedContentFields = [];
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    if (array_search($field, $mediumContentFields) !== false) {
                        $addedContentFields[] = $field;
                    } else if (Arr::exists(Config::get("media.presets", []), $field)) {
                        // TODO: Lay eagera loading? https://stackoverflow.com/questions/47222168/setappends-on-relation-which-is-being-loaded-by-with-in-laravel
                    } else {
                        $relation->addSelect($field);
                    }
                }
            }
            if (count($addedContentFields) > 0) {
                $relation->appendContents($addedContentFields, $lang);
            } else {
                $relation->appendContents(['title'], $lang);
            }
        }]);
    }

    /**********************
     * PUBLIC METHODS
     *********************/


    public function addRelation($relationData) {
        if (!isset($relationData['caller_entity_id'])) {
            $relationData["caller_entity_id"] = $this->getId();
        }
        self::createRelation($relationData);
    }

    public static function createRelation($relationData) {
        if (!isset($relationData['caller_entity_id'])) {
            throw new HttpException(422, 'createRelation: caller_entity_id is needed');
        }
        if (!isset($relationData['called_entity_id'])) {
            throw new HttpException(422, 'createRelation: called_entity_id is needed');
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
        self::incrementEntityVersion($relationData['caller_entity_id']);
        self::incrementRelationsVersion($relationData['called_entity_id']);
    }
    public function getContentFields() {
        return $this->contentFields ?? [];
    }
    public function getPropertiesFields() {
        return $this->propertiesFields ?? [];
    }

    /**
     * Adds content rows related to an Entity.
     *
     * @param  array $contents An array of one or more contents, for example ["title" => ["en" => "The title", "es" => "El título"], "slug" => ["en" => "the-title", "es" => "el-titulo"]] or without language defined if using the default one or explicit set as the second param ["title" => "The title", "slug" => "the-title"]
     * @param  string $lang optional language code, for example "en" or "es-mx"
     */
    public function addContents($contents, $lang = NULL)
    {
        $lang = $lang ?? Config::get('cms.langs')[0] ?? '';
        $routeToAdd = false;
        foreach ($contents as $key=>$value) {
            if (is_numeric($key)) {
                EntityContent::updateOrCreate(
                    [
                        "entity_id" => $this->getId(),
                        "field" => $value['field'],
                        "lang" => $value['lang'],
                    ],
                    [
                        "text" => $value['text'],
                    ]
                );
                if ($value['field'] == 'slug') {
                    $routeToAdd = [
                        "lang" => $value['lang'],
                        "slug" => $value['text']
                    ];
                }
            } else if (gettype($value) === 'array') {
                foreach ($value as $lang => $text) {
                    $this->addContents([ $key => $text], $lang);
                }
            } else if (gettype($value) === 'string') {
                EntityContent::updateOrCreate(
                    [
                        "entity_id" => $this->getId(),
                        "field" => $key,
                        "lang" => $lang
                    ],
                    [
                        "text" => $value
                    ]
                );
                if ($key == 'slug') {
                    $routeToAdd = [
                        "lang" => $lang ,
                        "slug" => $value
                    ];
                }
            }
            if ($routeToAdd) {
                Route::where('entity_id', $this->getId())->where('default', true)->where('lang', $lang)->delete();
                $parent_route = Route::where('entity_id', $this->parent_entity_id)->where('lang', $lang)->where('default', true)->first();
                $parent_route_path = $parent_route ? $parent_route->path === '/' ? '' : $parent_route->path : '';
                Route::create([
                    "entity_id" => $this->getId(),
                    "entity_model" => $this->model,
                    "path" => $parent_route_path."/".$routeToAdd['slug'],
                    "lang" => $routeToAdd['lang'],
                    "default" => true
                ]);
            }
        }
    }

    /**
     * Adds relation rows related to an Entity.
     *
     * @param  array $relations An array of one or more relations, for example ["entity_called_id" => "theShortId", "kind" => "medium", "depth": 0, "position" => 0]
     */
    public function replaceRelations($relations)
    {
        EntityRelation::where('caller_entity_id', $this->getId())
            ->where('kind', "!=", 'ancestor')
            ->delete();
        foreach ($relations as $relation) {
            $this->addRelation($relation);
        }
    }

    /**********************
     * RELATIONS
     *********************
     * @param null $kind
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|mixed
     */
    public function entities_related($kind = null)
    {
        return $this->belongsToMany('App\Models\Entity', 'relations', 'caller_entity_id', 'called_entity_id')
            ->using('App\Models\EntityRelation')
            ->as('relation')
            ->withPivot('kind', 'position', 'depth', 'tags')
            ->when($kind, function ($q) use ($kind) {
                return $q->where('kind', $kind);
            })
            ->when($kind === null, function ($q) use ($kind) {
                return $q->where('kind', '!=', EntityRelation::RELATION_ANCESTOR);
            })
            ->withTimestamps();
    }
    public function entities_relating($kind = null) {
        return $this->belongsToMany('App\Models\Entity', 'relations', 'called_entity_id', 'caller_entity_id')
            ->using('App\Models\EntityRelation')
            ->as('relation')
            ->withPivot('kind', 'position', 'depth', 'tags')
            ->when($kind, function ($q) use ($kind) {
                return $q->where('kind', $kind);
            })
            ->when($kind === null, function ($q) use ($kind) {
                return $q->where('kind', '!=', EntityRelation::RELATION_ANCESTOR);
            })
            ->withTimestamps();
    }
    public function entity_relations() {
        return $this->hasMany('App\Models\EntityRelation', 'caller_entity_id', 'id')
            ->where('kind', '!=', EntityRelation::RELATION_ANCESTOR);
    }
    public function media() {
        return $this->entities_related(EntityRelation::RELATION_MEDIA);
    }
    public function medium($tag=null, $lang=null) {
        return $this->belongsToOne('App\Models\Medium', 'relations', 'caller_entity_id', 'called_entity_id')
            ->using('App\Models\EntityRelation')
            ->as('relation')
            ->withPivot('kind', 'position', 'depth', 'tags')
            ->where('kind', EntityRelation::RELATION_MEDIA);
    }
    public function routes() {
        return $this->hasMany('App\Models\Route', 'entity_id', 'id');
    }
    public function route() {
        return $this->hasOne('App\Models\Route', 'entity_id', 'id')
            ->where('default', true);
    }
    public function contents($lang = null) {
        return $this->hasMany('App\Models\EntityContent', 'entity_id', 'id')
            ->when($lang !== null, function ($q) use ($lang) {
                return $q->where('lang', $lang);
            });
    }
    public function archives() {
        return $this->hasMany('App\Models\EntityArchive', 'entity_id', 'id');
    }

    /***********************
     * PRIVATE METHODS
     *********************/

    /**
     * Returns the id of the instance, if none is defined, it creates one
     */
    private function getId() {
        if (!isset($this->id)) {
            $this->id = Str::uuid();
        }
        return $this->id;
    }

    /**
     * Set stored contents to be saved once ready
     * @param $contents
     */
    private function setContents($contents) {
        $this->storedContents = $contents;
    }
    private function getContents() {
        return $this->storedContents;
    }

    /**
     * Set stored relations to be saved once ready
     * @param $contents
     */
    private function setStoredRelations($contents) {
        $this->storedRelations = $contents;
    }
    private function getStoredRelations() {
        return $this->storedRelations;
    }

    private static function incrementEntityVersion($entity_id) {
        $e = DB::table('entities')
            ->where('id', $entity_id);
        $e->increment('version');
        $e->increment('version_full');
        self::incrementTreeVersion($entity_id);
        self::incrementRelationsVersion($entity_id);
    }
    private static function incrementTreeVersion($entity_id) {
        $ancestors = Entity::select('id')->ancestorOf($entity_id)->get();
        if (!empty($ancestors)) {
            foreach ($ancestors as $ancestor) {
                $e = DB::table('entities')
                    ->where('id', $ancestor['id']);
                $e->increment('version_tree');
                $e->increment('version_full');
            }
        }
    }
    private static function incrementRelationsVersion($entity_id) {
        $relating = Entity::select('id')->relating($entity_id)->get();
        if (!empty($relating)) {
            foreach ($relating as $entityRelating) {
                $e = DB::table('entities')
                    ->where('id', $entityRelating['id']);
                $e->increment('version_relations');
                $e->increment('version_full');
                $ancestors = Entity::select('id')->ancestorOf($entityRelating->id)->get();
                if (!empty($ancestors)) {
                    foreach ($ancestors as $ancestor) {
                        $e = DB::table('entities')
                            ->where('id', $ancestor['id']);
                        $e->increment('version_full');
                    }
                }
            }
        }
    }

    /***********************
     * BOOT
     *********************/
    protected static function boot()
    {
        $modelName = Str::camel(Str::afterLast(get_called_class(), '\\'));
        if ($modelName !== 'entity') {
            static::addGlobalScope($modelName, function (Builder $builder) use ($modelName) {
                $builder->where('model', $modelName);
            });
        }
        parent::boot();
        static::creating(function (Model $entity) {
            // Set the default id as uuid
            if (!isset($entity[$entity->getKeyName()])) {
                do {
                    $id = Shortid::generate(Config::get('cms.shortIdLength', 10));
                    $found_duplicate = Entity::where($entity->getKeyName(), $id)->first();
                } while (!!$found_duplicate);
                $entity->setAttribute($entity->getKeyName(), $id);
            } else {
                $entity->setAttribute($entity->getKeyName(), substr($entity[$entity->getKeyName()], 0, 16));
            }
            //Throw an error if not model is defined on create
            if (!isset($entity['model'])) {
                throw new HttpException(422, 'A model name is requiered to create a new entity');
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
        self::saving(function ($entity) {
            if (isset($entity['contents'])) {
                $entity->setContents($entity['contents']);
                unset($entity['contents']);
            }
            if (isset($entity['relations'])) {
                $entity->setStoredRelations($entity['relations']);
                unset($entity['relations']);
            }
        });
        self::saved(function ($entity) {
            // Saving contents
            if ($entity->getContents() && count($entity->getContents()) > 0) {
                $entity->addContents($entity->getContents());
            }
            // Saving relations
            if ($entity->getStoredRelations() && count($entity->getStoredRelations()) > 0) {
                $entity->replaceRelations($entity->getStoredRelations());
            }
            $parentEntity = Entity::with('routes')->find($entity['parent_entity_id']);
            // Create the ancestors relations
            if ($parentEntity && isset($entity['parent_entity_id']) && $entity['parent_entity_id'] != NULL && $entity->isDirty('parent_entity_id')) {
                EntityRelation::where("caller_entity_id", $entity->id)->where('kind', EntityRelation::RELATION_ANCESTOR)->delete();
                EntityRelation::create([
                    "caller_entity_id" => $entity->id,
                    "called_entity_id" => $parentEntity->id,
                    "kind" => EntityRelation::RELATION_ANCESTOR,
                    "depth" => 1
                ]);
                $depth = 2;
                $ancestors = Entity::select('id')->ancestorOf($parentEntity->id)->orderBy('ancestor_relation_depth')->get();
                foreach ($ancestors as $ancestor) {
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
            if (isset($entity->properties['slug'])) {
                Route::where('entity_id', $entity->id)->where('default', true)->delete();
                foreach ($entity->properties['slug'] as $lang => $slug) {
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
            // Update versions
            self::incrementEntityVersion($entity->id);
        });
    }
}
