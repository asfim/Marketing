<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchBalance extends Model
{
    protected $fillable =
            [
                'total_amount',
                'branchId',
                'user_id'
            ];

    public $timestamps = true;
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }
}
