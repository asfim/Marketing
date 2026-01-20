<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CashStatement extends Model
{
    use LogsActivity;
    protected $fillable = [
       'transaction_id',
       'table_name', 
       'posting_date', 
       'debit',
       'credit',
       'balance',
       'branchId',
       'description',
        'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'cash_statement';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }
    public function bankInfo()
{
    return $this->belongsTo(BankInfo::class, 'bank_info_id');
}

}
