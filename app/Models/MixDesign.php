<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MixDesign extends Model
{
    use LogsActivity;
    protected $fillable = [
        'customer_id', 'psi', 'stone_id', 'stone_quantity', 'chemical_id',
        'chemical_quantity', 'cement_id', 'cement_quantity', 'sand_id',
        'sand_quantity', 'water', 'water_quantity', 'rate', 'description', 'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'mix_design';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function challans()
    {
        return $this->hasMany(ProductSale::class, 'mix_design_id', 'id');
    }
    
    
}
