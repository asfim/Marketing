<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Asset extends Model
{
    use LogsActivity;
    protected $fillable = [
        'transaction_id', 'asset_id', 'name', 'purchase_date', 'asset_type_id',
        'purchase_amount', 'salvage_value', 'asset_life_year', 'depreciation',
        'depreciated_amount', 'payment_mode', 'description', 'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'asset';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function asset_type()
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }

}
