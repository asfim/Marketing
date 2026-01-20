<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class StockAdjustment extends Model
{
    use LogsActivity;
    public function product_name() {
        return $this->belongsTo(ProductName::class, 'product_id', 'id');
    }

    protected static $logFillable = true;
    protected static $logName = 'stock_adjustment';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
}
