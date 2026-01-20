<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductConsumption extends Model
{
    use LogsActivity;

    protected static $logFillable = true;
    protected static $logName = 'product_consumption';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function product_names()
    {
        return $this->belongsTo(ProductName::class, 'product_id', 'id');
    }
}

