<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class OwnerProfile extends Model
{
    use LogsActivity;

    protected static $logFillable = true;
    protected static $logName = 'owner_profile';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function owner_payment_log()
    {
        return $this->hasMany(OwnerPaymentLog::class, 'profile_id', 'id');
    }
    public function rentInfos()
    {
        return $this->hasMany(OwnerPayment::class, 'profile_id', 'id');
    }

    public function paidAmount()
    {
        return $this->owner_payment_log()->sum('paid_amount');
    }

    public function locations()
    {
        return $this->hasMany(Location::class,'profile_id','id');
    }
}
