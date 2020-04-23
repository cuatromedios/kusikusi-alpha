<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
    public static function extensionIsImage($extension) {
        return array_search(strtolower($extension), ['jpeg', 'jpg', 'png', 'gif']) !== false;
    }

}
