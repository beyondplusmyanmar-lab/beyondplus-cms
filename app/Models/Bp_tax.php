<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bp_tax extends Model
{
    protected $primaryKey = 'tax_id';

    protected $table = 'bp_taxes';

    protected $fillable = [
        'tax_id', 'parent_id', 'tax_name', 'tax_link', 'tax_icon', 'tax_lan', 'tax_type', 'translate_id', 'lang', 'tax_active', 'tax_created',
    ];

    public function post()
    {
        return $this->belongsTo(Bp_post::class, 'post_id', 'id');
    }

    public function posts()
    {
        return $this->belongsToMany(Bp_post::class, 'bp_relationships', 'tax_id', 'post_id');
    }

    public function translate()
    {
        return $this->belongsTo(Bp_tax::class, 'tax_id', 'translate_id');
    }
}
