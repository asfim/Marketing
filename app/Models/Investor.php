<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'transaction_id',
        'address',
        'total_investment',
        'total_investment_return',
        'posting_date',
        'bank_id',
        'branchId',
    ];

    public function bank()
    {
        return $this->belongsTo(BankInfo::class, 'bank_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId');
    }
}
