<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Section extends EntityModel
{
    protected $contentFields = [ "title", "summary", "body" ];
    protected $propertiesFields = [];

}
