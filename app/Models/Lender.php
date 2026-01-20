<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;  // Add this import to use Eloquent features

class Lender extends Model  // Make sure it extends Model
{
    // Define the table name if it's not inferred from the model name
    protected $table = 'lenders';

    protected $fillable = [
        'name',
        'phone',
        'address',
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
