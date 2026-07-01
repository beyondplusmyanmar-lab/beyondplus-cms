<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity;

class SiteSettings extends Model
{
//    use LogsActivity;
    
    protected static $logAttributes = ['id'];

    protected static $recordEventes = ['created','updated','deleted'];

    protected static $logName = 'site setting';
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'site_settings';

    /**
    * The database primary key value.
    *
    * @var string
    */
    public function getDescriptionForEvent($eventName)
    {
        return "  has been {$eventName} site setting.";
    }

    protected $primaryKey = 'id';

    public $timestamps = false;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['shipping_weight', 'shipping_dimensions', 'default_currency', 'default_language', 'coupon_module', 'promotion_module','reward_module','stock_limit',
    ];

    public function currency()
    {
        return $this->hasOne('App\Currency','id','default_currency');
    }
    
}
