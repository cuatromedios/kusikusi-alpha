<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Product extends EntityModel
{
    const MODEL_NAME = 'product';
    protected $contentFields = [ "title", "summary" ];
    protected $propertiesFields = [ "price" ];

    /**
     * @param $key
     * @return string Returns a public path to the medium using the Icon preset
     */
    public function getIconAttribute($key)
    {
        return "/media/$this->id/icon/image.jpg";
    }

}
