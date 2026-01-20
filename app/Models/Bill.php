<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Bill extends Model
{
    use LogsActivity;
//    protected $fillable = [
//        'transaction_id',
//        'concrete_no',
//        'total_cuM',
//        'total_cft',
//        'total_amount',
//        'bill_date',
//        'concrete_method',
//        'customer_id',
//        'user_id'
//    ];
    protected $fillable = [
        'transaction_id',
        'concrete_no',
        'total_cuM',
        'total_cft',
        'total_amount',
        'bill_date',
        'concrete_method',
        'customer_id',
        'user_id',
        'pump_charge',
        'eng_tips',
        'vat',
        'ait',
        'description',

        'total_amount_before_discount'
    ];


    protected static $logFillable = true;
    protected static $logName = 'customer_bill';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
