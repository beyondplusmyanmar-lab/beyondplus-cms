<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'avatar', 'password', 'api_token', 'role', 'created_at',
    ];

    /**
     * Hidden when the model is serialised. This class maps to the same `users`
     * table as App\User, which already hides these; keep the two in step.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];
}
