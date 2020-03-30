<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medium extends Model
{
    protected $fillable = [
        'size', 'format', 'lang'
    ];
    protected $content = [
        'title', 'description', 'url'
    ];
}
