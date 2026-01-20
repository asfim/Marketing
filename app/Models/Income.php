<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Income extends Model
{
    use LogsActivity;
    protected $fillable = [
        'transaction_id',
        'income_name',
        'table_name',
        'date',
        'income_type_id',
        'payment_mode',
        'amount',
        'description',
        'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'income';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function income_type()
    {
        return $this->belongsTo(IncomeType::class, 'income_type_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }

    public function cash_statements()
    {
        return $this->belongsTo(CashStatement::class, 'transaction_id', 'transaction_id');
    }

    public function bank_statements()
    {
        return $this->belongsTo(BankStatement::class, 'transaction_id', 'transaction_id');
    }

}
