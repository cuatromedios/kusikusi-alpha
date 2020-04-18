<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UsesUuid;

class EntityArchive extends Model
{
    use UsesUuid;

    protected $table = 'archive';

    public function entity () {
        return $this->belongsTo('App\Models\Entity');
    }

    public static function archive($entity_id) {
        $entityToArchive = Entity::with('entityContents')->with('routes')->with('entitiesRelated')->find($entity_id);
        EntityArchive::create([
            "entity_id" => $entity_id,
            "version" => $entityToArchive->version,
            "payload" => $entityToArchive
        ]);
    }
}