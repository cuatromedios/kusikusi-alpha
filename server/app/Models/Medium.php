<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Medium extends EntityModel
{
    protected $appends = ['icon'];
    protected $contentFields = [ "title", "welcome", "footer" ];
    protected $propertiesFields = [ "size", "lang", "format", "length" ];

    /**
     * @param $key
     * @return string Returns a public path to the medium using the Icon preset
     */
    public function getIconAttribute($key)
    {
        return "/media/$this->id/icon/image.jpg";
    }

}
