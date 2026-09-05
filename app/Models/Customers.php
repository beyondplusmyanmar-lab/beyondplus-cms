<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customers extends Authenticatable
{
    use Notifiable;

    protected $guard = 'customer_web';

    protected $table = 'customers';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_types_id', 'first_name', 'last_name', 'gender', 'date_of_birth', 'email', 'phone', 'password', 'status', 'subscribed_to_news_letter', 'is_verified', 'profile_photo', 'total_reward_points', 'wallets', 'total_subtotal_amount', 'reward_expiry_date', 'activation_code', 'otpcode',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function customerAddresses()
    {
        return $this->hasMany('App\Models\Addresses', 'id', 'customers_id');
    }

    public function addresses()
    {
        return $this->belongsTo('App\Models\Addresses', 'id', 'customers_id');
    }

    public function customerType()
    {
        return $this->hasOne(CustomerTypes::class, 'id', 'customer_types_id');
    }

    // public function events()
    // {
    //     return $this->hasMany('App\Events','customers_id');
    // }

    // public function likes()
    // {
    //     return $this->hasMany('App\Like','customers_id');
    // }

    public function wishlist()
    {
        return $this->hasMany('App\Models\Wishlist');
    }
}
