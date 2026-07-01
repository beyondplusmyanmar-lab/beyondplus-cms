<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'faqs';

    protected $fillable = [

         'title', 'type', 'content', 'salt','translate_id','provider_id', 'created_at'

    ];


}
