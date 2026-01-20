<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankInstallmentInfo extends Model
{

    protected $fillable = [
        'installment_name',
        'installment_number',
        'installment_paid',
        'monthly_amount',
        'interest_rate',
        'total_loan',
        'total_loan_paid',
        'start_date',
        'end_date',
        'status',
        'bank_id',
        'file',
        'description',
        'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'bank_loan';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function bank_info()
    {
        return $this->belongsTo(BankInfo::class, 'bank_id', 'id');
    }
    public function bank_installment_log()
    {
        return $this->hasMany(BankInstallmentLog::class, 'id', 'installment_info_id');
    }

}
