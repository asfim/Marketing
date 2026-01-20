<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class BankInfo extends Model
{
    use LogsActivity;
    protected $fillable = [
        'account_name',
        'account_no',
        'bank_name',
        'branch_name',
        'account_type',
        'description',
        'status',
        'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'bank';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function bankStatements(){
        return $this->hasMany(BankStatement::class,'bank_info_id');
    }

    public function balance(){
        return $this->bankStatements()->sum(DB::raw('credit - debit'));
    }
}
