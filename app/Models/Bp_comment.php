<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Bp_comment extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'bp_comment';

    protected $fillable = [
        'post_id', 'user_id', 'body',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }
}
