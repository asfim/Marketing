<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{
    use LogsActivity;
    protected  $fillable    = ['name', 'address', 'balance','is_main_branch'];

    public $timestamps  = false;

    protected static $logFillable = true;
    protected static $logName = 'branch';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function cashStatements(){
        return $this->hasMany(CashStatement::class,'branchId');
    }

    public function balance(){
        return $this->cashStatements()->sum(DB::raw('credit - debit'));
    }
}
