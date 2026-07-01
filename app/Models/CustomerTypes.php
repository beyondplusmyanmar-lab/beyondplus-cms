<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity;

class CustomerTypes extends Model
{
    // use LogsActivity;

    protected static $logAttributes = ['name'];

    protected static $recordEventes = ['created','updated','deleted'];

    protected static $logName = 'customer type';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customer_types';

    public function getDescriptionForEvent($eventName)
    {
        return "  has been {$eventName} customer type.";
    }

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'discount_amount', 'total_spend_amount', 'status'];
}
