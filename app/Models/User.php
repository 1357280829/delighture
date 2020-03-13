<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'account', 'password', 'nickname', 'phone', 'email'
    ];
}
