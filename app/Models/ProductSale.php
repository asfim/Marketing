<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductSale extends Model
{
    use LogsActivity;
    protected $fillable = [
        'challan_no',
        'customer_id',
        'project_id',
        'sell_date',
        'mix_design_id',
        'cuM',
        'concrete_no',
        'status',
        'user_id',
        'rate'
    ];

    protected static $logFillable = true;
    protected static $logName = 'product_sale';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function branch() {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }
    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function project() {
        return $this->belongsTo(CustomerProject::class, 'project_id', 'id');
    }
    public function mix_design() {
        return $this->belongsTo(MixDesign::class, 'mix_design_id', 'id');
    }
    public function demo_bill() {
        return $this->belongsTo(DemoProductSale::class, 'challan_no', 'challan_no');
    }
    
    
}
