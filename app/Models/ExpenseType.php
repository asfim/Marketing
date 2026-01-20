<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ExpenseType extends Model
{
    use LogsActivity;
    protected $fillable = [
       'type_name', 
       'description',
        'category',
       'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'expense_type';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
}
