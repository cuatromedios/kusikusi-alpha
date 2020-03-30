<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'name', 'email',
    ];

    protected $hidden = [
        'password',
    ];

    protected $content = [
        'title'
    ];
}
