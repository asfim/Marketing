<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BankInstallmentLog extends Model
{
    use LogsActivity;
    protected $fillable = [
        'transaction_id',
        'bank_id',
        'installment_info_id',
        'posting_date',
        'paid_amount',
        'payment_mode',
        'cheque_no',
        'cheque_date',
        'file',
        'description',
        'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'bank_loan_payment';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function bank_installment_info()
    {
        return $this->belongsTo(BankInstallmentInfo::class, 'installment_info_id','id');
    }
}
