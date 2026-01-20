<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomerStatement extends Model
{
    use LogsActivity;
    protected $fillable = [
        'transaction_id',
        'posting_date',
        'description',
        'table_name',
        'debit',
        'credit',
        'balance',
        'customer_id',
          'user_id',
    ];

    protected static $logFillable = true;
    protected static $logName = 'customer_statement';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function bank_infos()
    {
        return $this->belongsTo(BankInfo::class, 'bank_id', 'id');
    }

    public function customer_payment()
    {
        return $this->hasOne(CustomerPayment::class, 'transaction_id','transaction_id');
    }
    public function bill()
    {
        return $this->hasOne(Bill::class, 'transaction_id','transaction_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }
}
