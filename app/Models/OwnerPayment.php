<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class OwnerPayment extends Model
{
    use LogsActivity;
    protected $fillable = [
        'location_id',
        'profile_id',
        'total_month',
        'monthly_amount',
        'payable_amount',
        'paid_amount',
        'due_amount',
        'description',
        'user_id',
        'status'
    ];

    protected static $logFillable = true;
    protected static $logName = 'owner_payment';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    //TODO:: Rename owner payments table to rent info
    //TODO:: Rename owner_payment_logs to rent_payment
    //TODO:: normalize owner_payments and owner_payment_logs(location_id,profile_id)

     public function getPayableAmountWithoutDueAttribute()
    {
        return $this->due_amount >= 0 ? $this->due_amount : 0;
    }

    public function getDueAmountOnlyAttribute()
    {
        return $this->due_amount < 0 ? abs($this->due_amount) : 0;
    }
    public function owner()
    {
        return $this->belongsTo(OwnerProfile::class,'profile_id','id');
    }
    public function location()
    {
        return $this->belongsTo(Location::class,'location_id','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
