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
    protected $fillable = ['caller_entity_id', 'called_entity_id', 'kind', 'position', 'depth', 'tags'];
    protected $casts = [
        'tags' => 'array'
    ];
    protected $hidden = array('created_at', 'updated_at', 'called_entity_id', 'caller_entity_id');
}
