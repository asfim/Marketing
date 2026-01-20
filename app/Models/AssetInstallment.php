<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class AssetInstallment extends Model
{
    use LogsActivity;
    protected $fillable = [
       'transaction_id', 'asset_id', 'name', 'date', 'installment_amount',
       'payment_mode', 'description', 'user_id', 'branchId'
    ];

    protected static $logFillable = true;
    protected static $logName = 'asset_installment';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
}
