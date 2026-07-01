<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity;

class Bp_languages extends Model
{
//    use LogsActivity;

    protected static $logAttributes = ['language_iso','language_value'];

    protected static $recordEventes = ['created','updated','deleted'];

    protected static $logName = 'language';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bp_languages';

    /**
     * The database primary key value.
     *
     * @var string
     */
    
    // public function getDescriptionForEvent($eventName)
    // {
    //     return "  has been {$eventName} language.";
    // }

    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['id','language_iso', 'language_value'];
}
