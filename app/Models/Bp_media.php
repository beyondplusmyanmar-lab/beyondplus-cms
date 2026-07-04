<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bp_media extends Model
{
    use LogsActivity;

    protected $primaryKey = 'media_id';
    protected $table = 'bp_media';

    protected $fillable = [
    	 'media_name','media_link', 'media_type','media_weight','media_description','media_created','department_type','created_at','updated_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('media')
            ->logOnly(['media_name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => trim(sprintf(
                '%s the media “%s”',
                $event === 'created' ? 'uploaded' : $event,
                \Illuminate\Support\Str::limit((string) $this->media_name, 60)
            )));
    }
}
