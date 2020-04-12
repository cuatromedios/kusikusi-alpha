<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Home extends EntityModel
{
    protected $contentFields = [ "title", "welcome" ];
    protected $propertiesFields = [];

}
