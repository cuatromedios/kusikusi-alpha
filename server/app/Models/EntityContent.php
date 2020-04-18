<?php

namespace App\Models;

use App\Models\Traits\UsesShortId;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EntityContent extends Pivot
{
    use UsesShortId;


    protected $table = 'contents';

    /**
     * To avoid "ambiguous" SQL errors Change the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'content_id';
    }
    protected $fillable = ['entity_id', 'lang', 'field', 'text'];
    protected $hidden = array('created_at', 'updated_at', 'entity_id', 'content_id');

    public function entity($lang = null) {
        return $this->belongsTo('App\Models\Entity', 'entity_id', 'id');
    }
}
