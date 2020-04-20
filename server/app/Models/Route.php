<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UsesShortId;

class Route extends Model
{
    use UsesShortId;
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'route_id', 'entity_id', 'entity_model'];
    protected $fillable = ['entity_id', 'path', 'entity_model', 'lang', 'default'];

    /**
     * To avoid "ambiguous" SQL errors Change the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'route_id';
    }
    public function entity () {
        return $this->belongsTo('App\Models\Entity');
    }
}
