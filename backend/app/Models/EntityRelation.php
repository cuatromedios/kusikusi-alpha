<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class EntityRelation extends Pivot
{
    const RELATION_ANCESTOR = 'ancestor';
    const RELATION_MEDIA = 'medium';
    const RELATION_UNDEFINED = 'relation';

    protected $table = 'relations';
    public $incrementing = false;
    protected $fillable = ['caller_entity_id', 'called_entity_id', 'kind', 'position', 'depth', 'tags'];
    protected $casts = [
        'tags' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $entity) {
            // Set the default id as uuid
            if (!isset($entity[$entity->getKeyName()])) {
                $entity->setAttribute($entity->getKeyName(), Str::uuid());
            }
        });

    }
}
