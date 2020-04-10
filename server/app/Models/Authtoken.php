<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Authtoken extends Model
{
    protected $fillable = [];

    public function user () {
        return $this->belongsTo('App\Models\User');
    }
}
