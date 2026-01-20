<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    // Define the table name if it's not inferred from the model name
    protected $table = 'borrowers';

    protected $fillable = [
        'name',
        'phone',
        'address',
        'loan_amount',
        'loan_taken_date',
        'loan_status',
        'user_id',
        'bank_id',
        'branchId',
    ];

    // Define the relationship with the bank
    public function bank()
    {
        return $this->belongsTo(BankInfo::class, 'bank_id');
    }

    // Define the relationship with the branch
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId');
    }
}
