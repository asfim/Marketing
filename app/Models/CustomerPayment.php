<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomerPayment extends Model
{
    use LogsActivity;

    protected static $logFillable = true;
    protected static $logName = 'customer_payment';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function bank_info()
    {
        return $this->belongsTo(BankInfo::class, 'bank_id', 'id');
    }
}
