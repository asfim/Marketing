<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Expense extends Model
{
    use LogsActivity;
    protected $fillable = [
        'transaction_id',
        'expense_name',
        'table_name',
        'date',
        'expense_type_id',
        'payment_mode',
        'amount',
        'branchId',
        'description',
        'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'expense';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function expense_type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }

    public function engineer_tips_statements()
    {
        return $this->belongsTo(EngineerTipsStatement::class, 'transaction_id', 'transaction_id');
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
