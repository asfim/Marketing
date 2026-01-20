<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class AssetType extends Model
{
    use LogsActivity;
    protected $fillable = [
       'name', 'description', 'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'asset_type';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function assets()
    {
        return $this->hasMany(Asset::class, 'asset_type_id');
    }

    public function asset_value()
    {
        return $this->assets()->sum(DB::raw('purchase_amount - depreciated_amount'));
    }
}
