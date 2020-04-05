<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

const TABLE = 'relations';

class EntityRelation extends Pivot
{
    const RELATION_ANCESTOR = 'ancestor';
    const RELATION_MEDIA = 'medium';
    const RELATION_UNDEFINED = 'relation';
    const TABLE = TABLE;

    protected $table = TABLE;
    // To avoid "ambiguos" errors
    protected $primaryKey = 'relation_id';
    public $incrementing = false;
    protected $fillable = ['caller_entity_id', 'called_entity_id', 'kind', 'position', 'depth', 'tags'];
    protected $casts = [
        'tags' => 'array'
    ];
    protected $hidden = array('created_at', 'updated_at');

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
