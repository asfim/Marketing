<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SupplierStatement extends Model
{
    use LogsActivity;
    protected $fillable = [
        'transaction_id',
        'posting_date',
        'description',
        'table_name',
        // 'debit',
        // 'credit',
        'balance',
        'supplier_id',
    ];

    protected static $logFillable = true;
    protected static $logName = 'supplier_statement';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function supplier_payment()
    {
        return $this->hasOne(SupplierPayment::class, 'transaction_id','transaction_id');
    }
    public function product_purchase()
    {
        return $this->hasOne(ProductPurchase::class, 'transaction_id','transaction_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }
}

