<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductStock extends Model
{
    use LogsActivity;
    protected $fillable = [
        'product_name_id ','quantity','consumption_qty','unit_type'
    ];

    protected static $logFillable = true;
    protected static $logName = 'product_stock';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function product_name() {
        return $this->belongsTo(ProductName::class, 'product_name_id', 'id');
    }
    
    public function supplier_statement(){
        return $this->hasMany(SupplierStatement::class, 'product_name_id', 'product_name_id');
    }
    
    public function product_purchase(){
        return $this->hasMany(ProductPurchase::class, 'product_name_id', 'product_name_id');
    }
    public function stock_adjustment(){
        return $this->hasMany(StockAdjustment::class, 'product_id', 'product_name_id');
    }
    
    public function product_cosumptions()
    {
        return $this->hasMany(ProductConsumption::class, 'product_id', 'product_name_id');
    }
}
