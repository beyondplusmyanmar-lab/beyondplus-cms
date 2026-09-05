<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bp_post extends Model
{
    use LogsActivity;

    protected $primaryKey = 'id';

    protected $table = 'bp_posts';

    protected $fillable = [

        'title', 'body', 'featured', 'featured_img', 'post_link', 'post_type', 'post_template', 'post_weight', 'post_active', 'translate_id', 'staff_id', 'lang', 'event_color', 'event_at', 'created_at',

    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('content')
            ->logOnly(['title'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => trim(sprintf(
                '%s the %s “%s”',
                $event,                                   // created / updated / deleted
                $this->post_type ?: 'post',               // post / page / news / event
                Str::limit((string) $this->title, 60)
            )));
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Bp_tax::class, 'bp_relationships', 'post_id', 'tax_id');
    }

    public function translate()
    {
        return $this->belongsTo(Bp_post::class, 'id', 'translate_id');
    }

    public function comment()
    {
        return $this->hasMany(Bp_comment::class, 'post_id', 'id');
    }
}
