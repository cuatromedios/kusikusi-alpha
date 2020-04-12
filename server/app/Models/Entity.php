<?php

namespace App\Models;

/**
 * Class Entity The ready to use model that extends EntityModel.
 * Use this for general use.
 * @package App\Models
 */
class Entity extends EntityModel
{
    /**
     * @var array A list of columns from the entities tables and other joins needs to be casted
     */
    protected $casts = [
        'properties' => 'array',
        'tags' => 'array',
        'child_relation_tags' => 'array',
        'descendant_relation_tags' => 'array',
        'siblings_relation_tags' => 'array',
        'media_tags' => 'array',
        'relation_tags' => 'array',
        'is_active' => 'boolean'
    ];

    public function getContentAttribute($contents) {
        return "A";
    }
}
