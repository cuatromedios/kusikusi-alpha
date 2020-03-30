<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    protected $content = [
        'title', 'welcome', 'url'
    ];
    public function getContent ()
    {
        return $this->content;
    }
}
