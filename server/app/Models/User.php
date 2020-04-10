<?php

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    const PROFILE_ADMIN = 'admin';
    const PROFILE_EDITOR = 'editor';
    const PROFILE_USER = 'user';
    const PROFILE_GUEST = 'guest';

    protected $fillable = [
        'name', 'email'
    ];

    protected $hidden = [
        'password',
    ];
    public static function boot($preset = [])
    {
        parent::boot();

        self::saving(function ($user) {
            if (isset($user['password'])) {
                $user['password'] = Hash::make($user['password']);
            }
        });
    }
}
