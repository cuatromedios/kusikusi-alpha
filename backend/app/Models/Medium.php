<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Medium extends EntityModel
{
    public function getIconAttribute($key)
    {
        return "/media/$this->id/icon/image.jpg";
    }
    protected $appends = ['icon'];

    protected static function booted()
    {
        static::addGlobalScope('medium', function (Builder $builder) {
            $builder->where('model', 'medium');
        });
    }
}
