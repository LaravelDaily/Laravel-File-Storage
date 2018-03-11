<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 *
 * @package App
 * @property string $user
 * @property string $role
 * @property decimal $payment_amount
*/
class Payment extends Model
{
    protected $fillable = ['payment_amount', 'user_id', 'role_id'];
    
    

    /**
     * Set to null if empty
     * @param $input
     */
    public function setUserIdAttribute($input)
    {
        $this->attributes['user_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setRoleIdAttribute($input)
    {
        $this->attributes['role_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setPaymentAmountAttribute($input)
    {
        $this->attributes['payment_amount'] = $input ? $input : null;
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    
}
