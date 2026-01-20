<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomerProject extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name', 'address', 'customer_id', 'user_id',
    ];

    protected static $logFillable = true;
    protected static $logName = 'customer_project';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function challans()
    {
        return $this->hasMany(ProductSale::class, 'project_id', 'id');
    }
}
