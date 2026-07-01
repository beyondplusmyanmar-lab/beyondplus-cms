<?php

namespace App\Models;

//use App\CouponMail;
use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity;

class Currency extends Model
{
    // use LogsActivity;

    protected static $logAttributes = ['name','code'];

    protected static $recordEventes = ['created','updated','deleted'];

    protected static $logName = 'currency';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'currency';

    /**
     * The database primary key value.
     *
     * @var string
     */
    public function getDescriptionForEvent($eventName)
    {
        return "  has been {$eventName} currency.";
    }

    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'code', 'conversation_rate', 'symbol', 'status'];
}
