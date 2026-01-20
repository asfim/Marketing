<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductName extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name','photo','conversion_rate','unit_price','description','category','user_id'
    ];
    protected static $logFillable = true;
    protected static $logName = 'product';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
}
