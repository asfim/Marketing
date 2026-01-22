<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductPurchase extends Model
{
    use LogsActivity;

//    protected static $logFillable = true;
    protected static $logName = 'product_purchase';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function product_name()
    {
        return $this->belongsTo(ProductName::class, 'product_name_id', 'id');
    }
    public function branch()
    {
    return $this->belongsTo(Branch::class, 'branchId', 'id');
    }

}
