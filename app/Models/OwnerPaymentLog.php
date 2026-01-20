<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class OwnerPaymentLog extends Model
{
    use LogsActivity;

    protected static $logFillable = true;
    protected static $logName = 'owner_payment_log';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
    public function owner_profile()
    {
        return $this->belongsTo(OwnerProfile::class, 'profile_id','id');
    }
}
