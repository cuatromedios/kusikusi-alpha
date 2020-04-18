<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Str;
use App\Models\Traits\UsesShortId;
use PUGX\Shortid\Shortid;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, UsesShortId;

    const PROFILE_ADMIN = 'admin';
    const PROFILE_EDITOR = 'editor';
    const PROFILE_USER = 'user';
    const PROFILE_GUEST = 'guest';

    protected $fillable = [
        'name', 'email'
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function authenticate($email, $password, $ip = '')
    {
        $user = User::where('email', $email)->first();
        $hashed1 = Hash::make($password);
        $hashed2 = Hash::make($password);
        if ($user && Hash::check($password, $user->password)) {
            $token = base64_encode(Hash::make(Str::random(64)));
            $authtoken = new Authtoken();
            $authtoken->user_id = $user->id;
            $authtoken->token =$token;
            $authtoken->created_ip = $ip;
            // TODO: If the user is inactive or deleted don't allow login
            $user->authtokens()->save($authtoken);
            return ([
                "token" => $token,
                "user" => $user
            ]);
        } else {
            return FALSE;
        }
    }

    /**
     * Get the authokens of the User.
     */
    public function authtokens () {
        return $this->hasMany('App\Models\Authtoken');
    }

    public static function boot($preset = [])
    {
        parent::boot();
        static::creating(function (Model $entity) {
            if (!isset($entity[$entity->getKeyName()])) {
                $entity->setAttribute($entity->getKeyName(), Shortid::generate(Config::get('cms.shortIdLength', 10)));
            }
        });
        self::saving(function ($user) {
            if (isset($user['password'])) {
                $user['password'] = Hash::make($user['password']);
            }
        });
    }
}
