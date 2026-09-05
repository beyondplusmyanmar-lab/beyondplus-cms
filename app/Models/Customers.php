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

    /**
     * Display name for the front end. The table stores the parts separately.
     */
    public function getNameAttribute(): string
    {
        $name = trim(($this->first_name ?? '').' '.($this->last_name ?? ''));

        return $name !== '' ? $name : ($this->email ?: $this->phone ?: 'Customer');
    }

    /**
     * Alias for the stored profile image, matching the themes' $author->avatar.
     */
    public function getAvatarAttribute(): ?string
    {
        return $this->profile_photo;
    }

    public function customerType()
    {
        return $this->hasOne(CustomerTypes::class, 'id', 'customer_types_id');
    }
}
