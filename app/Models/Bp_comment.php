<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bp_comment extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'bp_comment';

    protected $fillable = [
        'post_id', 'user_id', 'body',
    ];

    /**
     * The customer who wrote the comment.
     *
     * user_id holds a customers.id: the "web" guard resolves through the
     * customers provider, so Auth::id() at write time is a Customers key.
     */
    public function author()
    {
        return $this->belongsTo(Customers::class, 'user_id', 'id');
    }
}
