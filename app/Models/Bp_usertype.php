<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bp_usertype extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'bp_usertype';
    public $timestamps = false;

    protected $fillable = [
    	'id','role'
    ];

}
