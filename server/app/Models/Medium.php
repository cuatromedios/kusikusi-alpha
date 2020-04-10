<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Medium extends EntityModel
{
    const MODEL_NAME = 'medium';

    /**
     * @param $key
     * @return string Returns a public path to the medium using the Icon preset
     */
    public function getIconAttribute($key)
    {
        return "/media/$this->id/icon/image.jpg";
    }
    protected $appends = ['icon'];
    protected $contentFields = [
        "title" => [ "multilang" => true ],
        "welcome" => [ "multilang" => true ],
        "footer" => [ "multilang" => true ],
    ];

    protected static function booted()
    {
        static::addGlobalScope(self::MODEL_NAME, function (Builder $builder) {
            $builder->where('model', self::MODEL_NAME);
        });
    }
}
