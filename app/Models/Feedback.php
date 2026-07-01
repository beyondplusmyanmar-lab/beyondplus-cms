<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'feedback';

    protected $fillable = [

        //  'title','description','provider_id','customer_id','salt'
         'name', 'email', 'phone', 'subject', 'message', 'post_id', 'user_id'

    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id');
    }

}
