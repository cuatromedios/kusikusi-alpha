<?php

namespace App\Models;

class Entity extends EntityModel
{
    protected $casts = [
        'properties' => 'array',
        'tags' => 'array',
        'child_relation_tags' => 'array',
        'descendant_relation_tags' => 'array',
        'siblings_relation_tags' => 'array',
        'media_tags' => 'array',
        'relation_tags' => 'array',
        'is_active' => 'boolean',
        'price' => 'integer'
    ];
}
