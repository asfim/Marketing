<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class BankStatement extends Model
{
    use LogsActivity;

    protected $fillable = [
        'transaction_id',
        'table_name',
        'posting_date',
        'debit',
        'credit',
        'balance',
        'bank_info_id',
        'cheque_no',
        'ref_date',
        'branchId',
        'description',
        'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'bank_statements';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }

    public function bank_info()
    {
        return $this->belongsTo(BankInfo::class, 'bank_info_id', 'id');
    }


}
