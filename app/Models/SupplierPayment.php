<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SupplierPayment extends Model
{
    use LogsActivity;

    protected static $logFillable = true;
    protected static $logName = 'supplier_payment';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function bank_info()
    {
        return $this->belongsTo(BankInfo::class, 'bank_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId','id');
    }
}
