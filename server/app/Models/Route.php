<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UsesUuid;

class Route extends Model
{
    use UsesUuid;
    protected $hidden = array('created_at', 'updated_at', 'deleted_at', 'id', 'entity_id', 'entity_model');
    public function entity () {
        return $this->belongsTo('App\Models\Entity');
    }
}
