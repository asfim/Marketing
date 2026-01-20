<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class InvestorPayment extends Model
{
    protected $fillable = ['investor_id', 'transaction_id', 'amount', 'type', 'via', 'bank_id', 'branchId', 'note', 'date'];

    public function investor()
    {
        return $this->belongsTo(Investment::class);
    }

    public function bank()
    {
        return $this->belongsTo(BankInfo::class, 'bank_id');
    }
}
