<?php

namespace App\models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Relations\Pivot;

const TABLE = 'relations';

class EntityRelation extends Pivot
{
    use UsesUuid;

    const RELATION_ANCESTOR = 'ancestor';
    const RELATION_MEDIA = 'medium';
    const RELATION_UNDEFINED = 'relation';
    const TABLE = TABLE;

    protected $table = TABLE;

    /**
     * To avoid "ambiguos" errors Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'relation_id';
    }
    protected $fillable = ['caller_entity_id', 'called_entity_id', 'kind', 'position', 'depth', 'tags'];
    protected $casts = [
        'tags' => 'array'
    ];
    protected $hidden = array('created_at', 'updated_at', 'called_entity_id', 'caller_entity_id');
}
