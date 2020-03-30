<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class Entity extends Model
{
    protected $table = 'entities';
    public $incrementing = false;
    protected $fillable = ['id', 'model', 'content', 'parent_entity', 'published', 'created_by', 'updated_by', 'published_at', 'unpublished_at'];
    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean'
    ];

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
            //Set now as the published date if not set
            if (!isset($entity['published_at'])) {
                $entity['published_at'] = Carbon::now();
            }
            //Look for a model
            $modelClass = Entity::getClassFromModelId($entity['model']);
            if (class_exists($modelClass)) {
                $e = new $modelClass;
                echo "\n" . $entity['model'] . "\n";
                if (method_exists($e, "getFillable")) {
                    echo "\n- fillable:\n";
                    print_r($e->getFillable());
                }
                if (method_exists($e, "getTable")) {
                    echo "\n- table:\n";
                    print_r($e->getTable());
                }
                if (method_exists($e, "getContent")) {
                    echo "\n- content:\n";
                    print_r($e->getContent());
                }
            };
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
    private static function getClassFromModelId($modelId)
    {
        if (isset($modelId) && $modelId != '') {
            return ("\\App\\Models\\" . (Str::studly($modelId)));
        } else {
            return NULL;
        }
    }
}
