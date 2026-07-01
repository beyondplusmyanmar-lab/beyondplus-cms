<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bp_media extends Model
{
    protected $primaryKey = 'media_id';
    protected $table = 'bp_media';

    protected $fillable = [
    	 'media_name','media_link', 'media_type','media_weight','media_description','media_created','department_type','created_at','updated_at'
    ];



}
