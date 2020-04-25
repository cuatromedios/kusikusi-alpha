<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UsesShortId;

class EntityArchive extends Model
{
    use UsesShortId;

    protected $table = 'archive';
    protected $fillable = ["entity_id", "version", "payload"];

    public function entity () {
        return $this->belongsTo('App\Models\Entity');
    }

    public static function archive($entity_id) {
        $entityToArchive = Entity::with('contents')->with('routes')->with('entities_related')->find($entity_id);
        EntityArchive::create([
            "entity_id" => $entity_id,
            "version" => $entityToArchive->version,
            "payload" => $entityToArchive
        ]);
    }
}
