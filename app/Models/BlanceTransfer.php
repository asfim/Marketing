<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BlanceTransfer extends Model
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
    ];

    protected static $logFillable = true;
    protected static $logName = 'balance_transfer';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }
}
